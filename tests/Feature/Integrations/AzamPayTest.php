<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\InvoiceBalance;
use App\Enums\SubscriptionStatus;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\Receipt;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\TenantIntegration;
use App\Tenancy\TenantContext;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

const AZAM_AUTH = 'https://authenticator-sandbox.azampay.co.tz/AppRegistration/GenerateToken';
const AZAM_CHECKOUT = 'https://sandbox.azampay.co.tz/azampay/mno/checkout';

beforeEach(function () {
    useTestHosts();

    $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($key, $this->azamPrivate);
    $this->azamPublic = openssl_pkey_get_details($key)['key'];

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->url = 'http://acme.somaplus.test';
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->inSchool = fn (Closure $work) => TenantContext::run($this->school, $work);

    ($this->inSchool)(fn () => (new TenantIntegration)->forceFill([
        'channel' => 'payments', 'driver' => 'azampay', 'enabled' => true,
        'settings' => ['environment' => 'sandbox', 'app_name' => 'Acme', 'client_id' => 'cid'],
        'secrets' => ['client_secret' => 'csecret', 'api_key' => 'akey'],
    ])->save());

    $this->invoice = ($this->inSchool)(function () {
        $world = makeExamWorld($this->school);
        $structure = FeeStructure::factory()->create(['academic_year_id' => $world['year']->id, 'term_id' => $world['term']->id, 'school_class_id' => $world['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $world['year']->id, 'school_class_id' => $world['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);

        return Invoice::query()->where('student_id', $student->id)->firstOrFail();
    });

    // Starts from a fresh HTTP client each time, so a later fake is not shadowed by the first.
    $this->fakeAzam = function (array $checkout = ['success' => true, 'transactionId' => 'TX1', 'message' => 'ok']) {
        Http::swap(new HttpFactory);
        Http::fake([
            AZAM_AUTH => Http::response(['data' => ['accessToken' => 'tok']]),
            AZAM_CHECKOUT => Http::response($checkout),
            'https://sandbox.azampay.co.tz/azampay/v1/public-key*' => Http::response(['publicKey' => $this->azamPublic]),
        ]);
    };
    ($this->fakeAzam)();

    $this->ask = fn (array $data = [], ?object $user = null, ?Invoice $invoice = null) => $this->actingAs($user ?? $this->accountant)
        ->post($this->url.'/fees/invoices/'.($invoice ?? $this->invoice)->id.'/mobile-money', $data + [
            'phone' => '0712 345 678', 'provider' => 'Airtel', 'amount' => '40000', 'idempotency_key' => (string) Str::uuid(),
        ]);
    $this->requests = fn () => ($this->inSchool)(fn () => PaymentRequest::query()->orderBy('id')->get());
    $this->payments = fn () => ($this->inSchool)(fn () => Payment::query()->get());

    $this->callback = function (array $body = [], array $options = []) {
        $body += ['utilityref' => ($this->requests)()->first()?->reference ?? 'none', 'externalreference' => 'TX1', 'transactionstatus' => 'success', 'operator' => 'Airtel', 'amount' => '40000', 'mnoreference' => 'MNO1', 'message' => 'done'];
        if (! isset($body['signature'])) {
            openssl_sign($body['utilityref'].$body['externalreference'].$body['transactionstatus'].$body['operator'], $signature, $options['key'] ?? $this->azamPrivate, OPENSSL_ALGO_SHA256);
            $body['signature'] = base64_encode($signature);
        }

        return $this->postJson(($options['base'] ?? 'http://somaplus.test').'/webhooks/azampay/'.($options['tenant'] ?? $this->school->id), $body);
    };
});

it('sends a prompt to the payer phone and keeps a pending request without the phone number', function () {
    ($this->ask)()->assertSessionHasNoErrors()->assertRedirect();

    $request = ($this->requests)()->sole();
    expect($request->status)->toBe('pending')->and($request->amount)->toBe(4000000)->and($request->provider)->toBe('Airtel')
        ->and($request->provider_reference)->toBe('TX1')->and($request->invoice_id)->toBe($this->invoice->id)
        ->and($request->reference)->toStartWith('SP')->and(($this->payments)())->toHaveCount(0)
        ->and(json_encode($request->getAttributes()))->not->toContain('712345678');

    Http::assertSent(fn (HttpRequest $sent) => $sent->url() === AZAM_CHECKOUT
        && $sent['accountNumber'] === '255712345678' && $sent['amount'] === 40000 && $sent['currency'] === 'TZS'
        && $sent['externalId'] === $request->reference && $sent['provider'] === 'Airtel'
        && $sent->hasHeader('X-API-Key', 'akey') && $sent->hasHeader('Authorization', 'Bearer tok'));
});

it('prompts only once when the same form is sent twice', function () {
    $key = (string) Str::uuid();

    ($this->ask)(['idempotency_key' => $key]);
    ($this->ask)(['idempotency_key' => $key]);

    expect(($this->requests)())->toHaveCount(1);
    Http::assertSentCount(2); // one token, one checkout
});

it('refuses a bad phone, network or amount without calling AzamPay', function (array $data, string $field) {
    ($this->ask)($data)->assertSessionHasErrors($field);

    expect(($this->requests)())->toHaveCount(0);
    Http::assertNothingSent();
})->with([
    'bad phone' => [['phone' => '12345'], 'phone'],
    'unknown network' => [['provider' => 'Fake'], 'provider'],
    'cents' => [['amount' => '40000.50'], 'amount'],
    'zero' => [['amount' => '0'], 'amount'],
    'words' => [['amount' => 'abc'], 'amount'],
    'above the balance' => [['amount' => '100001'], 'amount'],
]);

it('only lets people who can record payments ask, and not a read-only school', function () {
    ($this->ask)([], userWithRole($this->school, 'teacher'))->assertForbidden();
    ($this->ask)([], userWithRole($this->school, 'head_teacher'))->assertForbidden();

    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    ($this->ask)([], userWithRole($this->school, 'owner'))->assertForbidden();

    expect(($this->requests)())->toHaveCount(0);
});

it('refuses when AzamPay is not the active provider or the invoice is not in TZS', function () {
    ($this->inSchool)(fn () => TenantIntegration::query()->update(['driver' => 'signed_webhook']));
    ($this->ask)()->assertSessionHasErrors('provider');

    ($this->inSchool)(fn () => TenantIntegration::query()->update(['driver' => 'azampay']));
    ($this->inSchool)(fn () => Invoice::query()->update(['currency' => 'KES']));
    ($this->ask)()->assertSessionHasErrors('provider');

    expect(($this->requests)())->toHaveCount(0);
});

it('marks the request failed when AzamPay rejects it, records nothing and tells the user', function () {
    ($this->fakeAzam)(['success' => false, 'transactionId' => null, 'message' => 'Insufficient funds']);

    ($this->ask)()->assertSessionHasErrors('provider');

    expect(($this->requests)()->sole()->status)->toBe('failed')->and(($this->payments)())->toHaveCount(0);
});

it('records a signed successful callback as a mobile money integration payment with a receipt, once', function () {
    ($this->ask)();

    ($this->callback)()->assertOk()->assertJson(['status' => 'applied']);
    ($this->callback)()->assertOk()->assertJson(['status' => 'duplicate']);

    $payment = ($this->payments)()->sole();
    $request = ($this->requests)()->sole();
    expect($payment->source)->toBe('integration')->and($payment->method)->toBe('mobile_money')->and($payment->amount)->toBe(4000000)
        ->and($request->status)->toBe('succeeded')->and($request->payment_id)->toBe($payment->id)->and($request->mno_reference)->toBe('MNO1')
        ->and(($this->inSchool)(fn () => Receipt::query()->count()))->toBe(1)
        ->and(($this->inSchool)(fn () => InvoiceBalance::of($this->invoice->fresh())->paid))->toBe(4000000);
});

it('refuses a callback with a wrong or missing signature', function () {
    ($this->ask)();
    $other = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($other, $otherPrivate);

    ($this->callback)([], ['key' => $otherPrivate])->assertUnauthorized();
    ($this->callback)(['signature' => ''])->assertUnauthorized();
    ($this->callback)(['signature' => 'not-base64!!'])->assertUnauthorized();

    expect(($this->payments)())->toHaveCount(0)->and(($this->requests)()->sole()->status)->toBe('pending');
});

it('never records a success whose amount is not what was asked', function () {
    ($this->ask)();

    ($this->callback)(['amount' => '90000'])->assertOk();

    $request = ($this->requests)()->sole();
    expect($request->status)->toBe('failed')->and($request->failure_reason)->toBe('amount_mismatch')->and(($this->payments)())->toHaveCount(0);
});

it('marks a failed callback as failed and records nothing', function () {
    ($this->ask)();

    ($this->callback)(['transactionstatus' => 'failure', 'message' => 'Wrong PIN'])->assertOk();

    expect(($this->requests)()->sole()->status)->toBe('failed')->and(($this->requests)()->sole()->failure_reason)->toBe('Wrong PIN')->and(($this->payments)())->toHaveCount(0);
});

it('still records a late success after the prompt expired, because the money moved', function () {
    ($this->ask)();
    ($this->inSchool)(fn () => PaymentRequest::query()->update(['expires_at' => now()->subHour()]));
    expect(($this->requests)()->sole()->displayStatus())->toBe('expired');

    ($this->callback)()->assertOk()->assertJson(['status' => 'applied']);

    expect(($this->payments)())->toHaveCount(1)->and(($this->requests)()->sole()->displayStatus())->toBe('succeeded');
});

it('ignores a signed callback for an unknown reference', function () {
    ($this->callback)(['utilityref' => 'SPUNKNOWN', 'externalreference' => 'TXX'])->assertOk()->assertJson(['status' => 'unknown']);

    expect(($this->payments)())->toHaveCount(0);
});

it('answers 404 for a school host, an unknown school, a switched-off integration or another provider', function () {
    ($this->ask)();

    ($this->callback)([], ['base' => 'http://acme.somaplus.test'])->assertNotFound();
    ($this->callback)([], ['tenant' => '01HZZZZZZZZZZZZZZZZZZZZZZZ'])->assertNotFound();
    ($this->inSchool)(fn () => TenantIntegration::query()->update(['enabled' => false]));
    ($this->callback)()->assertNotFound();
    ($this->inSchool)(fn () => TenantIntegration::query()->update(['enabled' => true, 'driver' => 'selcom']));
    ($this->callback)()->assertNotFound();

    expect(($this->payments)())->toHaveCount(0);
});

it('still records money for a read-only school', function () {
    ($this->ask)();
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    ($this->callback)()->assertOk()->assertJson(['status' => 'applied']);

    expect(($this->payments)())->toHaveCount(1);
});

it('shows the request form and its history on the invoice only when AzamPay is active', function () {
    $this->actingAs($this->accountant)->get($this->url.'/fees/invoices/'.$this->invoice->id)->assertOk()->assertSee(__('integrations.azampay.request'));
    ($this->ask)();
    $this->actingAs($this->accountant)->get($this->url.'/fees/invoices/'.$this->invoice->id)->assertSee(__('integrations.azampay.statuses.pending'));

    ($this->inSchool)(fn () => TenantIntegration::query()->update(['enabled' => false]));
    $this->actingAs($this->accountant)->get($this->url.'/fees/invoices/'.$this->invoice->id)->assertOk()->assertDontSee(__('integrations.azampay.request'));
});

it('saves the AzamPay credentials from the page, never shows the secrets again and lists the callback address', function () {
    $owner = userWithRole($this->school, 'owner');
    $data = ['enabled' => '1', 'driver' => 'azampay', 'config' => ['environment' => 'production', 'app_name' => 'Acme', 'client_id' => 'cid2', 'client_secret' => 'super-secret-value', 'api_key' => 'api-key-value']];

    $this->actingAs($owner)->put($this->url.'/settings/integrations/payments', $data)->assertSessionHasNoErrors();
    $page = $this->actingAs($owner)->get($this->url.'/settings/integrations')->assertOk()->assertSee('/webhooks/azampay/'.$this->school->id);

    $row = ($this->inSchool)(fn () => TenantIntegration::query()->sole());
    expect($page->getContent())->not->toContain('super-secret-value')->not->toContain('api-key-value')
        ->and($row->settings['environment'])->toBe('production')->and($row->secrets['client_secret'])->toBe('super-secret-value');

    $this->actingAs($owner)->put($this->url.'/settings/integrations/payments', ['config' => ['environment' => 'nope'] + $data['config']] + $data)->assertSessionHasErrors('config.environment');
});
