<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\InvoiceBalance;
use App\Domain\Fees\SetControlNumber;
use App\Domain\Integrations\Payments\WebhookSignature;
use App\Enums\SubscriptionStatus;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentNotification;
use App\Models\Receipt;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\TenantIntegration;
use App\Tenancy\TenantContext;

const WEBHOOK_SECRET = 'webhook-secret-for-tests-1';

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->connect = fn (?Tenant $school = null, bool $enabled = true, string $secret = WEBHOOK_SECRET) => ($this->inSchool)(function () use ($enabled, $secret) {
        $row = new TenantIntegration;
        $row->forceFill(['channel' => 'payments', 'driver' => 'signed_webhook', 'enabled' => $enabled, 'settings' => [], 'secrets' => ['webhook_secret' => $secret]])->save();
    }, $school);
    ($this->connect)();

    // An invoice of TZS 100,000.00 for a new student in the given school.
    $this->invoiceFor = fn (?string $control, ?Tenant $school = null) => ($this->inSchool)(function () use ($control, $school) {
        $world = makeExamWorld($school ?? $this->school);
        $structure = FeeStructure::factory()->create(['academic_year_id' => $world['year']->id, 'term_id' => $world['term']->id, 'school_class_id' => $world['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $world['year']->id, 'school_class_id' => $world['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);
        $invoice = Invoice::query()->where('student_id', $student->id)->firstOrFail();
        if ($control !== null) {
            app(SetControlNumber::class)->handle($invoice, $control);
        }

        return $invoice->fresh();
    }, $school);

    $this->notice = fn (array $payload, ?Tenant $school = null, array $options = []) => (function () use ($payload, $school, $options) {
        $body = $options['body'] ?? json_encode($payload + ['control_number' => '991234567890', 'amount' => '40000', 'currency' => 'TZS', 'reference' => 'BANK-1']);
        $time = $options['time'] ?? time();
        $signature = $options['signature'] ?? WebhookSignature::sign($options['secret'] ?? WEBHOOK_SECRET, $time, $body);

        return $this->call('POST', ($options['base'] ?? 'http://somaplus.test').'/webhooks/payments/'.($options['tenant'] ?? ($school ?? $this->school)->id), [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_X_SOMAPLUS_TIMESTAMP' => (string) $time, 'HTTP_X_SOMAPLUS_SIGNATURE' => $signature,
        ], $body);
    })();
    $this->payments = fn (?Tenant $school = null) => ($this->inSchool)(fn () => Payment::query()->orderBy('id')->get(), $school);
});

it('records a signed notice as an integration payment with a receipt', function () {
    $invoice = ($this->invoiceFor)('991234567890');

    ($this->notice)([])->assertOk()->assertJson(['status' => 'applied']);

    $payment = ($this->payments)()->sole();
    $balance = ($this->inSchool)(fn () => InvoiceBalance::of($invoice->fresh()));
    expect($payment->source)->toBe('integration')->and($payment->amount)->toBe(4000000)->and($payment->method)->toBe('bank')
        ->and($payment->provider_reference)->toBe('BANK-1')->and($payment->tenant_id)->toBe($this->school->id)
        ->and(($this->inSchool)(fn () => Receipt::query()->count()))->toBe(1)
        ->and($balance->paid)->toBe(4000000)
        ->and(($this->inSchool)(fn () => PaymentNotification::query()->sole()->status))->toBe('applied');
});

it('records a resent notice only once', function () {
    ($this->invoiceFor)('991234567890');

    ($this->notice)([])->assertOk();
    ($this->notice)([])->assertOk();

    expect(($this->payments)())->toHaveCount(1)->and(($this->inSchool)(fn () => Receipt::query()->count()))->toBe(1);
});

it('refuses a bad signature, a tampered body and a stale timestamp without recording anything', function () {
    ($this->invoiceFor)('991234567890');
    $body = json_encode(['control_number' => '991234567890', 'amount' => '40000', 'currency' => 'TZS', 'reference' => 'BANK-1']);

    ($this->notice)([], null, ['secret' => 'some-other-secret-value'])->assertUnauthorized();
    ($this->notice)([], null, ['signature' => str_repeat('a', 64)])->assertUnauthorized();
    ($this->notice)([], null, ['body' => str_replace('40000', '90000', $body), 'signature' => WebhookSignature::sign(WEBHOOK_SECRET, time(), $body)])->assertUnauthorized();
    ($this->notice)([], null, ['time' => time() - 1000])->assertUnauthorized();

    expect(($this->payments)())->toHaveCount(0)->and(($this->inSchool)(fn () => PaymentNotification::query()->count()))->toBe(0);
});

it('answers 404 for an unknown school, a switched-off integration and a school without one', function () {
    ($this->notice)([], null, ['tenant' => '01HZZZZZZZZZZZZZZZZZZZZZZZ'])->assertNotFound();

    ($this->connect)($this->other, false);
    ($this->notice)([], $this->other)->assertNotFound();

    $third = Tenant::factory()->create(['slug' => 'gamma']);
    ($this->notice)([], $third)->assertNotFound();
});

it('only answers on the central address', function () {
    ($this->invoiceFor)('991234567890');

    ($this->notice)([], null, ['base' => 'http://acme.somaplus.test'])->assertNotFound();
    ($this->notice)([], null, ['base' => 'http://evil.example'])->assertNotFound();

    expect(($this->payments)())->toHaveCount(0);
});

it('keeps an unknown control number and applies it when the provider resends after the number is set', function () {
    $invoice = ($this->invoiceFor)(null);

    ($this->notice)([])->assertOk()->assertJson(['status' => 'unmatched']);
    expect(($this->payments)())->toHaveCount(0)->and(($this->inSchool)(fn () => PaymentNotification::query()->sole()->status))->toBe('unmatched');

    ($this->inSchool)(fn () => app(SetControlNumber::class)->handle($invoice, '991234567890'));
    ($this->notice)([])->assertOk()->assertJson(['status' => 'applied']);

    expect(($this->payments)())->toHaveCount(1)->and(($this->inSchool)(fn () => PaymentNotification::query()->count()))->toBe(1);
});

it('rejects a currency that is not the school currency', function () {
    ($this->invoiceFor)('991234567890');

    ($this->notice)(['currency' => 'USD'])->assertOk()->assertJson(['status' => 'rejected']);

    expect(($this->payments)())->toHaveCount(0)->and(($this->inSchool)(fn () => PaymentNotification::query()->sole()->reason))->toBe('currency_mismatch');
});

it('records an overpayment because the money really moved', function () {
    $invoice = ($this->invoiceFor)('991234567890');

    ($this->notice)(['amount' => '150000'])->assertOk()->assertJson(['status' => 'applied']);

    expect(($this->inSchool)(fn () => InvoiceBalance::of($invoice->fresh())->status()))->toBe('overpaid');
});

it('answers 422 to a signed body that is not a valid notice', function (array $payload) {
    ($this->invoiceFor)('991234567890');

    ($this->notice)($payload)->assertStatus(422);

    expect(($this->payments)())->toHaveCount(0);
})->with([
    'short control number' => [['control_number' => '123']],
    'numeric amount' => [['amount' => 40000]],
    'no reference' => [['reference' => '']],
    'bad currency' => [['currency' => 'T']],
]);

it('cannot pay another school invoice with its own webhook', function () {
    ($this->connect)($this->other, true, 'beta-secret-for-tests-123');
    $foreign = ($this->invoiceFor)('991234567890', $this->other);

    // Beta's signed notice names a control number that only exists in the other school.
    ($this->invoiceFor)(null);
    ($this->notice)([], $this->school)->assertOk()->assertJson(['status' => 'unmatched']);

    expect(($this->payments)($this->other))->toHaveCount(0)->and(($this->payments)())->toHaveCount(0)
        ->and($foreign->tenant_id)->toBe($this->other->id);

    // And a notice signed with the other school's secret does not work on this school's address.
    ($this->notice)([], $this->school, ['secret' => 'beta-secret-for-tests-123'])->assertUnauthorized();
});

it('still records money for a read-only school', function () {
    ($this->invoiceFor)('991234567890');
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    ($this->notice)([])->assertOk()->assertJson(['status' => 'applied']);

    expect(($this->payments)())->toHaveCount(1);
});

it('does not need a CSRF token', function () {
    ($this->invoiceFor)('991234567890');
    $this->withMiddleware();

    ($this->notice)([])->assertOk();
});
