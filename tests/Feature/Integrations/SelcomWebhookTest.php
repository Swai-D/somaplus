<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\InvoiceBalance;
use App\Domain\Fees\SetControlNumber;
use App\Enums\SubscriptionStatus;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\TenantIntegration;
use App\Tenancy\TenantContext;

const SELCOM_TOKEN = 'selcom-bearer-token-for-tests';

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->inSchool = fn (Closure $work) => TenantContext::run($this->school, $work);
    ($this->inSchool)(fn () => (new TenantIntegration)->forceFill([
        'channel' => 'payments', 'driver' => 'selcom', 'enabled' => true, 'settings' => [], 'secrets' => ['bearer_token' => SELCOM_TOKEN],
    ])->save());

    $this->invoice = ($this->inSchool)(function () {
        $world = makeExamWorld($this->school);
        $structure = FeeStructure::factory()->create(['academic_year_id' => $world['year']->id, 'term_id' => $world['term']->id, 'school_class_id' => $world['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create(['first_name' => 'Selcomkid', 'middle_name' => null, 'last_name' => 'Mwanga']);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $world['year']->id, 'school_class_id' => $world['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);
        $invoice = Invoice::query()->where('student_id', $student->id)->firstOrFail();
        app(SetControlNumber::class)->handle($invoice, '991234567890');

        return $invoice->fresh();
    });

    $this->call = fn (string $step, array $body = [], array $options = []) => $this->postJson(
        ($options['base'] ?? 'http://somaplus.test').'/webhooks/selcom/'.($options['tenant'] ?? $this->school->id).'/'.$step,
        $body + ['operator' => 'MPESA-TZ', 'transid' => 'T1', 'reference' => 'SELREF1', 'utilityref' => '991234567890', 'amount' => '40000', 'msisdn' => '255700000000'],
        array_key_exists('token', $options) ? ($options['token'] === null ? [] : ['Authorization' => 'Bearer '.$options['token']]) : ['Authorization' => 'Bearer '.SELCOM_TOKEN],
    );
    $this->payments = fn () => ($this->inSchool)(fn () => Payment::query()->get());
});

it('looks up an invoice by control number and returns the student name', function () {
    ($this->call)('lookup')->assertOk()->assertJson(['reference' => 'SELREF1', 'resultcode' => '000', 'result' => 'SUCCESS', 'name' => 'Selcomkid Mwanga']);
});

it('answers 010 for an unknown or malformed control number', function (string $control) {
    ($this->call)('lookup', ['utilityref' => $control])->assertOk()->assertJson(['resultcode' => '010']);
    ($this->call)('notification', ['utilityref' => $control])->assertOk()->assertJson(['resultcode' => '010']);

    expect(($this->payments)())->toHaveCount(0);
})->with(['unknown' => ['990000000000'], 'short' => ['123'], 'letters' => ['99123456789a']]);

it('validates the amount', function (mixed $amount, string $code) {
    ($this->call)('validation', ['amount' => $amount])->assertOk()->assertJson(['resultcode' => $code]);
})->with([
    'good text' => ['40000', '000'], 'good int' => [40000, '000'], 'cents' => ['40000.50', '000'],
    'zero' => ['0', '012'], 'negative' => ['-5', '012'], 'words' => ['abc', '012'], 'float' => [40000.5, '012'], 'missing' => [null, '012'],
]);

it('records a notified payment as a mobile money integration payment with a receipt, once', function () {
    ($this->call)('notification')->assertOk()->assertJson(['resultcode' => '000', 'result' => 'SUCCESS']);
    ($this->call)('notification')->assertOk()->assertJson(['resultcode' => '000']);

    $payment = ($this->payments)()->sole();
    expect($payment->source)->toBe('integration')->and($payment->method)->toBe('mobile_money')->and($payment->amount)->toBe(4000000)
        ->and($payment->provider_reference)->toBe('SELCOM-SELREF1')
        ->and(($this->inSchool)(fn () => InvoiceBalance::of($this->invoice->fresh())->paid))->toBe(4000000);
});

it('records money above the balance because it really moved', function () {
    ($this->call)('notification', ['amount' => '150000'])->assertOk()->assertJson(['resultcode' => '000']);

    expect(($this->inSchool)(fn () => InvoiceBalance::of($this->invoice->fresh())->status()))->toBe('overpaid');
});

it('refuses a missing or wrong bearer token', function (?string $token) {
    ($this->call)('notification', [], ['token' => $token])->assertUnauthorized();
    ($this->call)('lookup', [], ['token' => $token])->assertUnauthorized();

    expect(($this->payments)())->toHaveCount(0);
})->with([null, 'wrong-token-wrong-token', '']);

it('answers 404 for another provider, a switched-off integration, an unknown school or a school host', function () {
    ($this->call)('lookup', [], ['tenant' => '01HZZZZZZZZZZZZZZZZZZZZZZZ'])->assertNotFound();
    ($this->call)('lookup', [], ['base' => 'http://acme.somaplus.test'])->assertNotFound();

    ($this->inSchool)(fn () => TenantIntegration::query()->update(['enabled' => false]));
    ($this->call)('lookup')->assertNotFound();

    ($this->inSchool)(fn () => TenantIntegration::query()->update(['enabled' => true, 'driver' => 'signed_webhook']));
    ($this->call)('lookup')->assertNotFound();
});

it('refuses an unknown step', function () {
    ($this->call)('refund')->assertNotFound();
});

it('does not touch an invoice in another currency', function () {
    ($this->inSchool)(fn () => Invoice::query()->update(['currency' => 'KES']));

    ($this->call)('notification')->assertOk()->assertJson(['resultcode' => '010']);

    expect(($this->payments)())->toHaveCount(0);
});

it('still records money for a read-only school', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    ($this->call)('notification')->assertOk()->assertJson(['resultcode' => '000']);

    expect(($this->payments)())->toHaveCount(1);
});

it('saves the Selcom token from the page, never shows it again and lists the three addresses', function () {
    $owner = userWithRole($this->school, 'owner');
    $url = 'http://acme.somaplus.test';

    $response = $this->actingAs($owner)->put("{$url}/settings/integrations/payments", ['enabled' => '1', 'driver' => 'selcom', 'config' => ['bearer_token' => 'my-selcom-token-123456']]);
    $response->assertSessionHasNoErrors();

    $page = $this->actingAs($owner)->get("{$url}/settings/integrations")->assertOk()
        ->assertSee('/webhooks/selcom/'.$this->school->id.'/lookup')->assertSee('/webhooks/selcom/'.$this->school->id.'/notification');
    expect($page->getContent())->not->toContain('my-selcom-token-123456')
        ->and(($this->inSchool)(fn () => TenantIntegration::query()->sole()->secrets['bearer_token']))->toBe('my-selcom-token-123456');
});
