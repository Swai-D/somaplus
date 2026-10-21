<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\RecordPayment;
use App\Domain\Fees\ReversePayment;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentNotification;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Support\Str;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->worlds = new ArrayObject;
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    // One invoice of TZS 100,000.00 for a new student of the given school.
    $this->invoiceFor = fn (?Tenant $school = null) => ($this->inSchool)(function () use ($school) {
        $world = $this->worlds[($school ?? $this->school)->id] ??= makeExamWorld($school ?? $this->school);
        $structure = FeeStructure::factory()->create(['academic_year_id' => $world['year']->id, 'term_id' => $world['term']->id, 'school_class_id' => $world['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $world['year']->id, 'school_class_id' => $world['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);

        return Invoice::query()->where('student_id', $student->id)->firstOrFail();
    }, $school);

    $this->pay = fn (Invoice $invoice, string $amount, string $daysAgo = '0', string $method = 'cash', string $source = 'manual', ?Tenant $school = null) => ($this->inSchool)(
        fn () => app(RecordPayment::class)->handle($invoice, [
            'amount' => (int) round((float) $amount * 100), 'method' => $method, 'source' => $source,
            'paid_on' => now()->subDays((int) $daysAgo)->toDateString(), 'idempotency_key' => (string) Str::uuid(),
        ])['payment'],
        $school,
    );

    $this->report = fn (array $query = [], ?object $user = null) => $this->actingAs($user ?? $this->accountant)->get($this->url.'/fees/reports/collections'.($query === [] ? '' : '?'.http_build_query($query)));
});

it('totals what was collected today by default, by method and by source', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice, '10000', '0', 'cash');
    ($this->pay)($invoice, '20000', '0', 'mobile_money', 'integration');
    ($this->pay)($invoice, '5000', '3', 'bank'); // older: not in today's report

    $response = ($this->report)()->assertOk()->assertSee('TZS 30,000.00')->assertSee('TZS 10,000.00')->assertSee('TZS 20,000.00');

    expect($response->getContent())->not->toContain('TZS 35,000.00');
});

it('includes both end dates and nothing outside them', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice, '1000', '5');
    ($this->pay)($invoice, '2000', '3');
    ($this->pay)($invoice, '4000', '1');
    ($this->pay)($invoice, '8000', '0');

    $from = now()->subDays(3)->toDateString();
    $to = now()->subDays(1)->toDateString();

    $page = ($this->report)(['from' => $from, 'to' => $to])->assertOk()->assertSee('TZS 6,000.00')->assertSee($from)->assertSee($to);
    expect($page->getContent())->not->toContain('TZS 15,000.00')->not->toContain('TZS 14,000.00');
});

it('leaves reversed payments out of the total and lists them apart', function () {
    $invoice = ($this->invoiceFor)();
    $keep = ($this->pay)($invoice, '10000');
    $undo = ($this->pay)($invoice, '3000');
    $this->actingAs($this->accountant);
    ($this->inSchool)(fn () => app(ReversePayment::class)->handle($undo, 'Entered by mistake'));

    $page = ($this->report)()->assertOk()->assertSee('TZS 10,000.00')->assertSee('TZS 3,000.00');
    expect($page->getContent())->not->toContain('TZS 13,000.00')->and($keep->amount)->toBe(1000000);
});

it('shows what is still owed on invoices today, counting only unreversed payments', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice, '40000');
    $undo = ($this->pay)($invoice, '5000');
    $this->actingAs($this->accountant);
    ($this->inSchool)(fn () => app(ReversePayment::class)->handle($undo, 'Entered by mistake'));

    // One invoice of 100,000 with 40,000 paid (the reversed 5,000 does not count): 60,000 is still owed.
    ($this->report)()->assertOk()->assertSee('TZS 60,000.00')->assertSee('TZS 40,000.00');
});

it('never counts an overpaid invoice as negative debt', function () {
    $invoice = ($this->invoiceFor)();
    ($this->pay)($invoice, '150000', '0', 'bank', 'integration');

    ($this->report)()->assertOk()->assertSee('TZS 150,000.00');
    expect(($this->report)()->getContent())->not->toContain('TZS -');
});

it('lists provider notices that were not recorded, with their counts', function () {
    ($this->inSchool)(function () {
        (new PaymentNotification)->forceFill(['provider_reference' => 'LOST-1', 'control_number' => '991111111111', 'amount' => 2500000, 'currency' => 'TZS', 'status' => 'unmatched', 'reason' => 'unknown_control_number', 'received_at' => now()])->save();
        (new PaymentNotification)->forceFill(['provider_reference' => 'BAD-1', 'control_number' => '992222222222', 'amount' => 100, 'currency' => 'USD', 'status' => 'rejected', 'reason' => 'currency_mismatch', 'received_at' => now()])->save();
        (new PaymentNotification)->forceFill(['provider_reference' => 'OK-1', 'control_number' => '993333333333', 'amount' => 100, 'currency' => 'TZS', 'status' => 'applied', 'received_at' => now()])->save();
        (new PaymentNotification)->forceFill(['provider_reference' => 'OLD-1', 'control_number' => '994444444444', 'amount' => 100, 'currency' => 'TZS', 'status' => 'unmatched', 'reason' => 'unknown_control_number', 'received_at' => now()->subDays(10)])->save();
    });

    ($this->report)()->assertOk()->assertSee('LOST-1')->assertSee('BAD-1')->assertSee('TZS 25,000.00')
        ->assertDontSee('OK-1')->assertDontSee('OLD-1')->assertSee(__('integrations.notices.reasons.currency_mismatch'));
});

it('says so when there is nothing to report', function () {
    ($this->report)()->assertOk()->assertSee(__('fees.report.none'))->assertSee(__('fees.report.exceptions_none'));
});

it('does not mix in another school', function () {
    $mine = ($this->invoiceFor)();
    $theirs = ($this->invoiceFor)($this->other);
    ($this->pay)($mine, '1000');
    ($this->pay)($theirs, '777777', '0', 'bank', 'integration', $this->other);
    ($this->inSchool)(fn () => (new PaymentNotification)->forceFill(['provider_reference' => 'THEIR-1', 'status' => 'unmatched', 'received_at' => now()])->save(), $this->other);

    $page = ($this->report)()->assertOk()->assertSee('TZS 1,000.00')->assertDontSee('THEIR-1');
    expect($page->getContent())->not->toContain('777,777');
});

it('is for people who can see fees only', function (string $role, bool $allowed) {
    $response = ($this->report)([], userWithRole($this->school, $role));

    $allowed ? $response->assertOk() : $response->assertForbidden();
})->with([
    'owner' => ['owner', true],
    'accountant' => ['accountant', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', false],
    'parent' => ['parent_student', false],
]);

it('needs sign-in', function () {
    $this->get($this->url.'/fees/reports/collections')->assertRedirect($this->url.'/login');
});

it('refuses bad dates, a reversed range and more than a year', function (array $query) {
    ($this->report)($query)->assertSessionHasErrors();
})->with([
    'not a date' => [['from' => 'yesterday']],
    'reversed' => [['from' => '2026-10-10', 'to' => '2026-10-01']],
    'too long' => [['from' => '2024-01-01', 'to' => '2026-01-01']],
]);

it('shows in the menu and in Swahili', function () {
    ($this->report)()->assertSee(route('fees.reports.collections'));
    $this->actingAs($this->accountant)->withSession(['locale' => 'sw'])->get($this->url.'/fees/reports/collections')->assertOk()
        ->assertSee(__('menu.collections_report', [], 'sw'))->assertSee(__('fees.report.intro', [], 'sw'));
});

it('does not write anything to the audit log or change any payment', function () {
    $invoice = ($this->invoiceFor)();
    $payment = ($this->pay)($invoice, '1000');
    $before = ($this->inSchool)(fn () => Payment::query()->count());

    ($this->report)()->assertOk();

    expect(($this->inSchool)(fn () => Payment::query()->count()))->toBe($before)->and($payment->amount)->toBe(100000);
});
