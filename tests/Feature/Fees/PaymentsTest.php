<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\InvoiceBalance;
use App\Domain\Fees\RecordPayment;
use App\Domain\Fees\SetControlNumber;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentReversal;
use App\Models\Receipt;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->owner = userWithRole($this->school, 'owner');
    $this->w = makeExamWorld($this->school);

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    // An invoice of TZS 100,000.00 (10,000,000 minor units) for a new student.
    $this->invoiceFor = fn (string $name = 'Payer', ?string $control = null, ?Tenant $school = null, ?array $world = null) => ($this->inSchool)(function () use ($name, $control, $world) {
        $world ??= $this->w;
        $structure = FeeStructure::factory()->create([
            'academic_year_id' => $world['year']->id, 'term_id' => $world['term']->id, 'school_class_id' => $world['form1']->id,
        ]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create(['first_name' => $name]);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $world['year']->id, 'school_class_id' => $world['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);
        $invoice = Invoice::query()->where('student_id', $student->id)->firstOrFail();
        if ($control !== null) {
            app(SetControlNumber::class)->handle($invoice, $control);
        }

        return $invoice->fresh();
    }, $school);

    $this->pay = fn (Invoice $invoice, array $data = [], ?object $user = null) => $this->actingAs($user ?? $this->accountant)
        ->post("{$this->url}/fees/invoices/{$invoice->id}/payments", $data + [
            'amount' => '40000', 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => (string) Str::uuid(),
        ]);
    $this->payments = fn () => ($this->inSchool)(fn () => Payment::query()->orderBy('id')->get());
    $this->balance = fn (Invoice $invoice) => ($this->inSchool)(fn () => InvoiceBalance::of($invoice->fresh()));
});

it('records a payment in minor units, issues receipt 1 and leaves the invoice untouched', function () {
    $invoice = ($this->invoiceFor)();

    ($this->pay)($invoice, ['amount' => '40000.50', 'reference' => 'SLIP-77'])->assertSessionHasNoErrors()->assertRedirect();

    $payment = ($this->payments)()->sole();
    $receipt = ($this->inSchool)(fn () => Receipt::query()->sole());
    $balance = ($this->balance)($invoice);
    expect($payment->amount)->toBe(4000050)
        ->and($payment->currency)->toBe('TZS')->and($payment->method)->toBe('cash')->and($payment->source)->toBe('manual')
        ->and($payment->reference)->toBe('SLIP-77')->and($payment->tenant_id)->toBe($this->school->id)
        ->and($payment->recorded_by)->toBe($this->accountant->id)
        ->and($receipt->payment_id)->toBe($payment->id)->and($receipt->reference())->toBe('REC-000001')
        ->and($balance->paid)->toBe(4000050)->and($balance->balance())->toBe(5999950)->and($balance->status())->toBe('partial')
        ->and(($this->inSchool)(fn () => Invoice::query()->findOrFail($invoice->id)->total))->toBe(10000000);
});

it('works out paid, balance and status from the payments', function () {
    $invoice = ($this->invoiceFor)();
    expect(($this->balance)($invoice)->status())->toBe('unpaid');

    ($this->pay)($invoice, ['amount' => '30000']);
    ($this->pay)($invoice, ['amount' => '70000']);

    $balance = ($this->balance)($invoice);
    expect($balance->paid)->toBe(10000000)->and($balance->balance())->toBe(0)->and($balance->status())->toBe('paid');
});

it('refuses to take the balance below zero and does not use up a receipt number', function () {
    $invoice = ($this->invoiceFor)();

    ($this->pay)($invoice, ['amount' => '100000.01'])->assertSessionHasErrors('amount');
    ($this->pay)($invoice, ['amount' => '100000'])->assertSessionHasNoErrors();
    ($this->pay)($invoice, ['amount' => '1'])->assertSessionHasErrors('amount');

    expect(($this->payments)())->toHaveCount(1)->and(($this->inSchool)(fn () => Receipt::query()->pluck('number')->all()))->toBe([1]);
});

it('refuses amounts that are not plain positive numbers', function (string $amount) {
    $invoice = ($this->invoiceFor)();

    ($this->pay)($invoice, ['amount' => $amount])->assertSessionHasErrors('amount');

    expect(($this->payments)())->toHaveCount(0)->and(($this->inSchool)(fn () => Receipt::query()->count()))->toBe(0);
})->with(['abc', '-5', '0', '0.00', '1e3', '12.345', '1 000', '1,000.00', '+5', '99999999999']);

it('refuses a bad method, a future date and a missing key', function () {
    $invoice = ($this->invoiceFor)();

    ($this->pay)($invoice, ['method' => 'barter'])->assertSessionHasErrors('method');
    ($this->pay)($invoice, ['paid_on' => now()->addDay()->toDateString()])->assertSessionHasErrors('paid_on');
    ($this->pay)($invoice, ['paid_on' => '31/12/2026'])->assertSessionHasErrors('paid_on');
    ($this->pay)($invoice, ['idempotency_key' => ''])->assertSessionHasErrors('idempotency_key');

    expect(($this->payments)())->toHaveCount(0);
});

it('accepts a past payment date', function () {
    $invoice = ($this->invoiceFor)();

    ($this->pay)($invoice, ['paid_on' => now()->subDays(5)->toDateString()])->assertSessionHasNoErrors();

    expect(($this->payments)()->sole()->paid_on->toDateString())->toBe(now()->subDays(5)->toDateString());
});

it('never makes a second payment when the same form is sent again', function () {
    $invoice = ($this->invoiceFor)();
    $key = (string) Str::uuid();

    ($this->pay)($invoice, ['idempotency_key' => $key])->assertSessionHas('status', __('fees.payments.recorded'));
    ($this->pay)($invoice, ['idempotency_key' => $key])->assertSessionHas('status', __('fees.payments.already'));
    ($this->pay)($invoice, ['idempotency_key' => $key])->assertSessionHasNoErrors();

    expect(($this->payments)())->toHaveCount(1)->and(($this->inSchool)(fn () => Receipt::query()->count()))->toBe(1)
        ->and(($this->balance)($invoice)->paid)->toBe(4000000);
});

it('refuses a key that is reused for a different amount or invoice', function () {
    $first = ($this->invoiceFor)('First');
    $second = ($this->invoiceFor)('Second');
    $key = (string) Str::uuid();

    ($this->pay)($first, ['idempotency_key' => $key])->assertSessionHasNoErrors();
    ($this->pay)($first, ['idempotency_key' => $key, 'amount' => '1'])->assertSessionHasErrors('idempotency_key');
    ($this->pay)($second, ['idempotency_key' => $key])->assertSessionHasErrors('idempotency_key');

    expect(($this->payments)())->toHaveCount(1);
});

it('gives back the payment a concurrent request already recorded', function () {
    $invoice = ($this->invoiceFor)();
    $data = ['amount' => 1000, 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'same-key-1'];

    $first = ($this->inSchool)(fn () => app(RecordPayment::class)->handle($invoice, $data));
    $second = ($this->inSchool)(fn () => app(RecordPayment::class)->handle($invoice, $data));

    expect($first['created'])->toBeTrue()->and($second['created'])->toBeFalse()->and($second['payment']->id)->toBe($first['payment']->id)
        ->and(($this->payments)())->toHaveCount(1);
});

it('stops a second payment with the same key at the database too', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice, ['idempotency_key' => 'dup-key']);

    expect(fn () => ($this->inSchool)(function () use ($invoice) {
        $copy = new Payment;
        $copy->forceFill(['invoice_id' => $invoice->id, 'amount' => 1, 'currency' => 'TZS', 'method' => 'cash', 'idempotency_key' => 'dup-key', 'paid_on' => now()])->save();
    }))->toThrow(QueryException::class);
});

it('gives receipt numbers without gaps, also across reversals and refused attempts', function () {
    $invoice = ($this->invoiceFor)();

    ($this->pay)($invoice, ['amount' => '10000'])->assertSessionHasNoErrors();
    ($this->pay)($invoice, ['amount' => 'bad'])->assertSessionHasErrors('amount');
    ($this->pay)($invoice, ['amount' => '20000'])->assertSessionHasNoErrors();
    $first = ($this->payments)()->first();
    $this->actingAs($this->owner)->post("{$this->url}/fees/payments/{$first->id}/reverse", ['reason' => 'Wrong student'])->assertSessionHasNoErrors();
    ($this->pay)($invoice, ['amount' => '30000'])->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => Receipt::query()->orderBy('number')->pluck('number')->all()))->toBe([1, 2, 3]);
});

it('numbers receipts per school', function () {
    $mine = ($this->invoiceFor)();
    ($this->pay)($mine);

    $foreign = makeExamWorld($this->other);
    $theirs = ($this->invoiceFor)('Theirs', null, $this->other, $foreign);
    TenantContext::run($this->other, fn () => app(RecordPayment::class)->handle($theirs, ['amount' => 100, 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'k1']));

    expect(($this->inSchool)(fn () => Receipt::query()->pluck('number')->all()))->toBe([1])
        ->and(TenantContext::run($this->other, fn () => Receipt::query()->pluck('number')->all()))->toBe([1]);
});

it('records a payment by control number', function () {
    $invoice = ($this->invoiceFor)('Bynumber', '991234567890');

    $this->actingAs($this->accountant)->post("{$this->url}/fees/payments", [
        'control_number' => '991234567890', 'amount' => '25000', 'method' => 'bank', 'paid_on' => now()->toDateString(),
        'reference' => 'NMB-1', 'idempotency_key' => (string) Str::uuid(),
    ])->assertSessionHasNoErrors()->assertRedirect();

    $payment = ($this->payments)()->sole();
    expect($payment->invoice_id)->toBe($invoice->id)->and($payment->method)->toBe('bank')->and($payment->control_number)->toBe('991234567890')
        ->and($payment->amount)->toBe(2500000);
});

it('refuses an unknown control number and one that belongs to another school', function () {
    ($this->invoiceFor)('Known', '991234567890');
    $foreign = makeExamWorld($this->other);
    ($this->invoiceFor)('Theirs', '770000000001', $this->other, $foreign);
    $post = fn (string $number) => $this->actingAs($this->accountant)->post("{$this->url}/fees/payments", [
        'control_number' => $number, 'amount' => '1000', 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => (string) Str::uuid(),
    ]);

    $post('123456789')->assertSessionHasErrors('control_number');
    $post('770000000001')->assertSessionHasErrors('control_number');
    $post('')->assertSessionHasErrors('control_number');

    expect(($this->payments)())->toHaveCount(0);
});

it('reverses a payment without removing it or its receipt, and the balance recovers', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice, ['amount' => '100000']);
    $payment = ($this->payments)()->sole();
    expect(($this->balance)($invoice)->status())->toBe('paid');

    $this->actingAs($this->owner)->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => 'Entered twice'])
        ->assertSessionHasNoErrors()->assertRedirect("{$this->url}/fees/invoices/{$invoice->id}");

    $balance = ($this->balance)($invoice);
    expect($balance->paid)->toBe(0)->and($balance->status())->toBe('unpaid')
        ->and(($this->payments)())->toHaveCount(1)
        ->and(($this->inSchool)(fn () => Receipt::query()->count()))->toBe(1)
        ->and(($this->inSchool)(fn () => PaymentReversal::query()->sole()->reason))->toBe('Entered twice');

    ($this->pay)($invoice, ['amount' => '100000'])->assertSessionHasNoErrors(); // the full amount can be paid again
    expect(($this->balance)($invoice)->status())->toBe('paid');
});

it('treats a second reversal as harmless and logs it once', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice);
    $payment = ($this->payments)()->sole();

    $this->actingAs($this->owner)->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => 'First reason']);
    $this->actingAs($this->owner)->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => 'Second reason'])->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => PaymentReversal::query()->count()))->toBe(1)
        ->and(($this->inSchool)(fn () => PaymentReversal::query()->sole()->reason))->toBe('First reason')
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'payment.reversed')->count()))->toBe(1);
});

it('refuses a reversal without a reason', function (string $reason) {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice);
    $payment = ($this->payments)()->sole();

    $this->actingAs($this->owner)->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => $reason])->assertSessionHasErrors('reason');

    expect(($this->inSchool)(fn () => PaymentReversal::query()->count()))->toBe(0);
})->with(['', 'ab', '   ']);

it('keeps a payment and its receipt when its invoice is deleted behind the scenes', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice);

    expect(fn () => ($this->inSchool)(fn () => Invoice::query()->whereKey($invoice->id)->delete()))->toThrow(QueryException::class);
});

it('records money from an integration even beyond the balance, once per provider reference', function () {
    $invoice = ($this->invoiceFor)();
    $record = fn (array $extra = []) => ($this->inSchool)(fn () => app(RecordPayment::class)->handle($invoice, $extra + [
        'amount' => 15000000, 'method' => 'bank', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'bank-1',
        'provider_reference' => 'BANK-REF-1', 'source' => 'integration',
    ]));

    $first = $record();
    $again = $record(['idempotency_key' => 'bank-retry']); // the provider sends the same notice again with another key

    expect($first['created'])->toBeTrue()->and($again['created'])->toBeFalse()->and($again['payment']->id)->toBe($first['payment']->id)
        ->and($first['payment']->source)->toBe('integration')
        ->and(($this->balance)($invoice)->status())->toBe('overpaid')->and(($this->balance)($invoice)->balance())->toBe(-5000000)
        ->and(($this->payments)())->toHaveCount(1);
});

it('refuses a provider reference that was already used for another amount or invoice', function () {
    $first = ($this->invoiceFor)('One');
    $second = ($this->invoiceFor)('Two');
    $base = ['amount' => 1000, 'method' => 'bank', 'paid_on' => now()->toDateString(), 'provider_reference' => 'REF-X', 'source' => 'integration'];

    ($this->inSchool)(fn () => app(RecordPayment::class)->handle($first, $base + ['idempotency_key' => 'a']));

    expect(fn () => ($this->inSchool)(fn () => app(RecordPayment::class)->handle($second, $base + ['idempotency_key' => 'b'])))->toThrow(ValidationException::class)
        ->and(fn () => ($this->inSchool)(fn () => app(RecordPayment::class)->handle($first, ['amount' => 2000, 'idempotency_key' => 'c'] + $base)))->toThrow(ValidationException::class);
});

it('lets only the right roles see, record and reverse payments', function (string $role, bool $view, bool $record, bool $reverse) {
    $user = userWithRole($this->school, $role);
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice);
    $payment = ($this->payments)()->sole();
    $receipt = ($this->inSchool)(fn () => Receipt::query()->sole());
    $this->actingAs($user);
    $check = fn (TestResponse $r, bool $allowed) => $allowed ? $r->assertSuccessful() : $r->assertForbidden();

    $check($this->get("{$this->url}/fees/payments"), $view);
    $check($this->get("{$this->url}/fees/receipts/{$receipt->id}"), $view);
    $record ? $this->get("{$this->url}/fees/payments/create")->assertRedirect() : $this->get("{$this->url}/fees/payments/create")->assertForbidden();

    $body = ['amount' => '1000', 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => (string) Str::uuid()];
    $response = $this->post("{$this->url}/fees/invoices/{$invoice->id}/payments", $body);
    $record ? $response->assertRedirect() : $response->assertForbidden();
    $response = $this->post("{$this->url}/fees/payments", $body + ['control_number' => '1']);
    $record ? $response->assertSessionHasErrors('control_number') : $response->assertForbidden();
    $response = $this->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => 'Because']);
    $reverse ? $response->assertRedirect() : $response->assertForbidden();
})->with([
    'owner' => ['owner', true, true, true],
    'accountant' => ['accountant', true, true, false],
    'head teacher' => ['head_teacher', true, false, false],
    'teacher' => ['teacher', false, false, false],
    'parent' => ['parent_student', false, false, false],
]);

it('keeps guests out', function () {
    $invoice = ($this->invoiceFor)();

    $this->get("{$this->url}/fees/payments")->assertRedirect("{$this->url}/login");
    $this->post("{$this->url}/fees/invoices/{$invoice->id}/payments", [])->assertRedirect("{$this->url}/login");
    $this->post("{$this->url}/fees/payments/1/reverse", [])->assertRedirect("{$this->url}/login");
});

it('answers 404 for a payment, receipt or invoice of another school', function () {
    $foreign = makeExamWorld($this->other);
    $theirs = ($this->invoiceFor)('Theirstudent', null, $this->other, $foreign);
    [$payment, $receipt] = TenantContext::run($this->other, function () use ($theirs) {
        $result = app(RecordPayment::class)->handle($theirs, ['amount' => 100, 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'k']);

        return [$result['payment'], $result['payment']->receipt()->firstOrFail()];
    });

    $this->actingAs($this->owner);
    $this->get("{$this->url}/fees/receipts/{$receipt->id}")->assertNotFound();
    $this->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => 'Attack'])->assertNotFound();
    $this->post("{$this->url}/fees/invoices/{$theirs->id}/payments", ['amount' => '1', 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'x'])->assertNotFound();
    $this->get("{$this->url}/fees/payments")->assertOk()->assertDontSee('Theirstudent');
    expect(TenantContext::run($this->other, fn () => PaymentReversal::query()->count()))->toBe(0);
});

it('lets a read-only school look at payments and receipts but not change them', function () {
    $invoice = ($this->invoiceFor)('Readonlykid');
    ($this->pay)($invoice);
    $payment = ($this->payments)()->sole();
    $receipt = ($this->inSchool)(fn () => Receipt::query()->sole());
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/fees/payments")->assertOk()->assertSee('Readonlykid');
    $this->actingAs($owner)->get("{$this->url}/fees/receipts/{$receipt->id}")->assertOk()->assertSee('REC-000001');
    ($this->pay)($invoice, [], $owner)->assertForbidden();
    $this->actingAs($owner)->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => 'Read only'])->assertForbidden();

    expect(($this->payments)())->toHaveCount(1)->and(($this->inSchool)(fn () => PaymentReversal::query()->count()))->toBe(0);
});

it('writes payments, reversals and receipt views to the audit log with ids only', function () {
    $invoice = ($this->invoiceFor)('Auditedpayer');
    ($this->pay)($invoice, ['reference' => 'SECRET-REF']);
    $payment = ($this->payments)()->sole();
    $receipt = ($this->inSchool)(fn () => Receipt::query()->sole());
    $this->actingAs($this->accountant)->get("{$this->url}/fees/receipts/{$receipt->id}")->assertOk();
    $this->actingAs($this->owner)->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => 'Secret reason']);

    $logs = ($this->inSchool)(fn () => AuditLog::query()->whereIn('action', ['payment.recorded', 'payment.reversed', 'receipt.viewed'])->orderBy('id')->get());
    expect($logs->pluck('action')->all())->toBe(['payment.recorded', 'receipt.viewed', 'payment.reversed'])
        ->and($logs->pluck('subject_id')->all())->toBe([$payment->id, $receipt->id, $payment->id])
        ->and(json_encode($logs->toArray()))->not->toContain('Auditedpayer')->not->toContain('SECRET-REF')->not->toContain('Secret reason');
});

it('does not log a refused receipt view or a refused payment', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice);
    $receipt = ($this->inSchool)(fn () => Receipt::query()->sole());
    $teacher = userWithRole($this->school, 'teacher');

    $this->actingAs($teacher)->get("{$this->url}/fees/receipts/{$receipt->id}")->assertForbidden();
    ($this->pay)($invoice, ['amount' => 'bad'])->assertSessionHasErrors('amount');

    expect(($this->inSchool)(fn () => AuditLog::where('action', 'receipt.viewed')->count()))->toBe(0)
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'payment.recorded')->count()))->toBe(1);
});

it('shows paid, balance and status on the invoice and in the list, ignoring reversed payments', function () {
    $invoice = ($this->invoiceFor)('Listed');
    ($this->pay)($invoice, ['amount' => '30000']);
    ($this->pay)($invoice, ['amount' => '20000']);
    $this->actingAs($this->owner)->post("{$this->url}/fees/payments/".($this->payments)()->last()->id.'/reverse', ['reason' => 'Mistake made']);

    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk()
        ->assertSee('TZS 30,000.00')->assertSee('TZS 70,000.00')->assertSee(__('fees.payments.statuses.partial'))->assertSee(__('fees.payments.reversed_badge'));
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices")->assertOk()
        ->assertSee('Listed')->assertSee('TZS 70,000.00')->assertSee(__('fees.payments.statuses.partial'));
});

it('hides the payment form on a fully paid invoice and from those who may not record', function () {
    $invoice = ($this->invoiceFor)();
    $form = __('fees.payments.record_button');

    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk()->assertSee($form);
    $this->actingAs(userWithRole($this->school, 'head_teacher'))->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk()->assertDontSee($form);
    ($this->pay)($invoice, ['amount' => '100000']);
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk()->assertDontSee($form);
});

it('shows a receipt with the payment details, the control number and no guardian data', function () {
    $invoice = ($this->invoiceFor)('Receiptkid', '991234567890');
    ($this->inSchool)(function () use ($invoice) {
        $guardian = Guardian::factory()->create(['name' => 'Secretguardian', 'phone' => '+255700111222']);
        $invoice->student->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);
    });
    ($this->pay)($invoice, ['amount' => '12345.60', 'method' => 'mobile_money', 'reference' => 'MPESA-9']);
    $receipt = ($this->inSchool)(fn () => Receipt::query()->sole());

    $this->actingAs($this->accountant)->get("{$this->url}/fees/receipts/{$receipt->id}")->assertOk()
        ->assertSee('REC-000001')->assertSee('Receiptkid')->assertSee('TZS 12,345.60')->assertSee('MPESA-9')->assertSee('991234567890')
        ->assertDontSee('Secretguardian')->assertDontSee('255700111222');
});

it('marks a reversed payment on its receipt', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice);
    $payment = ($this->payments)()->sole();
    $receipt = ($this->inSchool)(fn () => Receipt::query()->sole());
    $this->actingAs($this->owner)->post("{$this->url}/fees/payments/{$payment->id}/reverse", ['reason' => 'Bounced cheque']);

    $this->actingAs($this->accountant)->get("{$this->url}/fees/receipts/{$receipt->id}")->assertOk()->assertSee('Bounced cheque');
});

it('lists the payments with their receipts and strikes out reversed ones', function () {
    $invoice = ($this->invoiceFor)('Payeelisted');
    ($this->pay)($invoice, ['amount' => '10000']);

    $this->actingAs($this->accountant)->get("{$this->url}/fees/payments")->assertOk()
        ->assertSee('REC-000001')->assertSee('Payeelisted')->assertSee('TZS 10,000.00');
});

it('shows the payment pages in Swahili and lists the menu only for those who may see it', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice);

    $this->actingAs($this->accountant)->withSession(['locale' => 'sw'])->get("{$this->url}/fees/payments")
        ->assertOk()->assertSee('Malipo')->assertSee('Risiti');
    $this->actingAs($this->accountant)->get("{$this->url}/")->assertOk()->assertSee('/fees/payments', false);
    $this->actingAs(userWithRole($this->school, 'teacher'))->get("{$this->url}/")->assertOk()->assertDontSee('/fees/payments', false);
});

it('gives the payment permissions to the right roles', function () {
    $can = function (string $role, string $permission) {
        $user = userWithRole($this->school, $role);

        return ($this->inSchool)(fn () => $user->can($permission));
    };

    expect($can('owner', 'payments.record'))->toBeTrue()->and($can('owner', 'payments.reverse'))->toBeTrue()
        ->and($can('accountant', 'payments.record'))->toBeTrue()->and($can('accountant', 'payments.reverse'))->toBeFalse()
        ->and($can('head_teacher', 'payments.record'))->toBeFalse()
        ->and($can('teacher', 'payments.record'))->toBeFalse();
});
