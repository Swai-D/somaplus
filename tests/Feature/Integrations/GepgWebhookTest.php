<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\InvoiceBalance;
use App\Domain\Fees\SetControlNumber;
use App\Domain\Integrations\Payments\Gepg\GepgMessage;
use App\Enums\SubscriptionStatus;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentNotification;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\TenantIntegration;
use App\Tenancy\TenantContext;

/** A throw-away key pair and self-signed certificate: [private key PEM, certificate PEM]. */
function gepgKeyPair(): array
{
    $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    $csr = openssl_csr_new(['commonName' => 'test'], $key);
    $cert = openssl_csr_sign($csr, null, $key, 1);
    openssl_pkey_export($key, $private);
    openssl_x509_export($cert, $certificate);

    return [$private, $certificate];
}

function gepgNotice(array $trx = [], string $spCode = '12345', ?string $privateKey = null, ?string $tamper = null): string
{
    $trx += ['PayCtrNum' => '991234567890', 'PaidAmt' => '40000.00', 'CCy' => 'TZS', 'PayRefId' => 'GEPG-REC-1', 'TrxDtTm' => now()->toDateString().'T10:15:00'];
    $inner = '';
    foreach ($trx + ['SpCode' => $spCode, 'BillId' => 'B1'] as $name => $value) {
        $inner .= "<{$name}>{$value}</{$name}>";
    }
    $element = "<gepgPmtSpInfo><PymtTrxInf>{$inner}</PymtTrxInf></gepgPmtSpInfo>";
    openssl_sign($element, $signature, $privateKey ?? test()->gepgPrivate, OPENSSL_ALGO_SHA1);

    return '<Gepg>'.($tamper === null ? $element : str_replace('40000.00', $tamper, $element)).'<gepgSignature>'.base64_encode($signature).'</gepgSignature></Gepg>';
}

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    [$this->gepgPrivate, $this->gepgCert] = gepgKeyPair();
    [$this->ourPrivate, $this->ourCert] = gepgKeyPair();
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    ($this->inSchool)(function () {
        (new TenantIntegration)->forceFill([
            'channel' => 'payments', 'driver' => 'gepg', 'enabled' => true,
            'settings' => ['sp_code' => '12345', 'sp_sys_id' => 'SP0001', 'gepg_certificate' => $this->gepgCert],
            'secrets' => ['signing_key' => $this->ourPrivate],
        ])->save();
    });

    $this->invoice = ($this->inSchool)(function () {
        $world = makeExamWorld($this->school);
        $structure = FeeStructure::factory()->create(['academic_year_id' => $world['year']->id, 'term_id' => $world['term']->id, 'school_class_id' => $world['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $world['year']->id, 'school_class_id' => $world['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);
        $invoice = Invoice::query()->where('student_id', $student->id)->firstOrFail();
        app(SetControlNumber::class)->handle($invoice, '991234567890');

        return $invoice->fresh();
    });

    $this->send = fn (string $body, ?string $base = null, ?string $tenant = null) => $this->call('POST', ($base ?? 'http://somaplus.test').'/webhooks/gepg/'.($tenant ?? $this->school->id), [], [], [], ['CONTENT_TYPE' => 'application/xml'], $body);
    $this->payments = fn () => ($this->inSchool)(fn () => Payment::query()->get());
});

it('records a verified GePG payment by control number and answers with a signed 7101 acknowledgement', function () {
    $response = ($this->send)(gepgNotice())->assertOk();

    $payment = ($this->payments)()->sole();
    expect($payment->source)->toBe('integration')->and($payment->amount)->toBe(4000000)->and($payment->provider_reference)->toBe('GEPG-REC-1')
        ->and(($this->inSchool)(fn () => InvoiceBalance::of($this->invoice->fresh())->paid))->toBe(4000000)
        ->and($response->getContent())->toContain('<TrxStsCode>7101</TrxStsCode>');

    // The reply is signed with the school's own key.
    $body = $response->getContent();
    expect(GepgMessage::valid('<gepgPmtSpInfoAck><TrxStsCode>7101</TrxStsCode></gepgPmtSpInfoAck>', GepgMessage::signature($body), $this->ourCert))->toBeTrue();
});

it('records a resent GePG notice once but still acknowledges it', function () {
    ($this->send)(gepgNotice())->assertOk();
    ($this->send)(gepgNotice())->assertOk()->assertSee('7101', false);

    expect(($this->payments)())->toHaveCount(1);
});

it('refuses a wrong signature, an altered body and a missing signature', function () {
    [$otherPrivate] = gepgKeyPair();

    ($this->send)(gepgNotice([], '12345', $otherPrivate))->assertUnauthorized();
    ($this->send)(gepgNotice([], '12345', null, '90000.00'))->assertUnauthorized();
    ($this->send)(preg_replace('~<gepgSignature>.*</gepgSignature>~s', '', gepgNotice()))->assertUnauthorized();
    ($this->send)('not xml')->assertUnauthorized();

    expect(($this->payments)())->toHaveCount(0);
});

it('refuses a verified notice that names another service provider code', function () {
    ($this->send)(gepgNotice([], '99999'))->assertStatus(422);

    expect(($this->payments)())->toHaveCount(0);
});

it('keeps a payment for an unknown control number visible and still acknowledges', function () {
    ($this->send)(gepgNotice(['PayCtrNum' => '991111111111', 'PayRefId' => 'GEPG-REC-2']))->assertOk()->assertSee('7101', false);

    expect(($this->payments)())->toHaveCount(0)
        ->and(($this->inSchool)(fn () => PaymentNotification::query()->sole()->status))->toBe('unmatched');
});

it('answers 404 when another provider is chosen, it is switched off, the school is unknown or the host is not central', function () {
    ($this->send)(gepgNotice(), null, '01HZZZZZZZZZZZZZZZZZZZZZZZ')->assertNotFound();
    ($this->send)(gepgNotice(), 'http://acme.somaplus.test')->assertNotFound();

    ($this->inSchool)(fn () => TenantIntegration::query()->update(['enabled' => false]));
    ($this->send)(gepgNotice())->assertNotFound();

    ($this->inSchool)(fn () => TenantIntegration::query()->update(['enabled' => true, 'driver' => 'signed_webhook']));
    ($this->send)(gepgNotice())->assertNotFound();

    expect(($this->payments)())->toHaveCount(0);
});

it('still records money for a read-only school', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    ($this->send)(gepgNotice())->assertOk();

    expect(($this->payments)())->toHaveCount(1);
});

it('saves the GePG settings from the page, keeps the private key hidden and shows the callback address', function () {
    $owner = userWithRole($this->school, 'owner');
    $url = 'http://acme.somaplus.test';
    $data = ['enabled' => '1', 'driver' => 'gepg', 'config' => ['sp_code' => '12345', 'sp_sys_id' => 'SP0001', 'gepg_certificate' => $this->gepgCert, 'signing_key' => $this->ourPrivate]];

    $this->actingAs($owner)->put("{$url}/settings/integrations/payments", $data)->assertSessionHasNoErrors()->assertRedirect();
    $this->actingAs($owner)->put("{$url}/settings/integrations/payments", ['config' => ['signing_key' => ''] + $data['config']] + $data)->assertSessionHasNoErrors();

    $row = ($this->inSchool)(fn () => TenantIntegration::query()->sole());
    expect($row->secrets['signing_key'])->toBe(trim($this->ourPrivate))->and($row->settings)->not->toHaveKey('signing_key');

    $page = $this->actingAs($owner)->get("{$url}/settings/integrations")->assertOk()->assertSee('/webhooks/gepg/'.$this->school->id);
    expect($page->getContent())->not->toContain('PRIVATE KEY');

    $this->actingAs($owner)->put("{$url}/settings/integrations/payments", ['config' => ['sp_code' => ''] + $data['config']] + $data)->assertSessionHasErrors('config.sp_code');
});
