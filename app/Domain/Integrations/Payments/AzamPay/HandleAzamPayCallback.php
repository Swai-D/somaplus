<?php

namespace App\Domain\Integrations\Payments\AzamPay;

use App\Domain\Fees\RecordPayment;
use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Support\Money;
use Illuminate\Support\Facades\DB;

/**
 * Applies AzamPay's result callback to a payment request. Must run inside the school's tenant context.
 *
 * Nothing in the body is trusted until its RSA signature checks out. The signature covers only the references, status
 * and operator, so the amount is checked against what was asked: a success with another amount is never recorded.
 * A request that already succeeded is never changed, and a late success (after the prompt expired) is still recorded
 * because the payer's money has moved. The payment goes through RecordPayment (idempotent, gapless receipt).
 */
class HandleAzamPayCallback
{
    public const APPLIED = 'applied';

    public const DUPLICATE = 'duplicate';

    public const UNKNOWN = 'unknown';

    public const INVALID_SIGNATURE = 'invalid_signature';

    public function __construct(private readonly RecordPayment $record) {}

    /**
     * @param  array<string, mixed>  $payload  the callback body
     */
    public function handle(array $payload): string
    {
        $client = AzamPayClient::forCurrentTenant();
        $utilityRef = $this->text($payload['utilityref'] ?? null);
        $externalRef = $this->text($payload['externalreference'] ?? null);
        $status = $this->text($payload['transactionstatus'] ?? null);
        $operator = $this->text($payload['operator'] ?? null);

        if ($client === null) {
            return self::UNKNOWN;
        }

        try {
            $valid = $client->verifyCallback($utilityRef, $externalRef, $status, $operator, $this->text($payload['signature'] ?? null));
        } catch (AzamPayException $e) {
            report($e);
            $valid = false;
        }

        if (! $valid) {
            return self::INVALID_SIGNATURE;
        }

        return DB::transaction(function () use ($payload, $utilityRef, $externalRef, $status): string {
            $request = PaymentRequest::query()->lockForUpdate()->where('reference', $utilityRef)->first()
                ?? ($externalRef === '' ? null : PaymentRequest::query()->lockForUpdate()->where('provider_reference', $externalRef)->first());

            if ($request === null) {
                return self::UNKNOWN;
            }
            if ($request->status === PaymentRequest::SUCCEEDED) {
                return self::DUPLICATE;
            }

            if (strtolower($status) !== 'success') {
                $request->forceFill([
                    'status' => PaymentRequest::FAILED,
                    'failure_reason' => mb_substr($this->text($payload['message'] ?? null) ?: 'failed', 0, 250),
                    'completed_at' => now(),
                ])->save();

                return self::APPLIED;
            }

            if ($this->minor($payload['amount'] ?? null) !== $request->amount) {
                $request->forceFill(['status' => PaymentRequest::FAILED, 'failure_reason' => 'amount_mismatch', 'completed_at' => now()])->save();

                return self::APPLIED;
            }

            $invoice = $request->invoice()->firstOrFail();
            $result = $this->record->handle($invoice, [
                'amount' => $request->amount,
                'method' => 'mobile_money',
                'paid_on' => now()->toDateString(),
                'reference' => $request->reference,
                'idempotency_key' => 'azampay:'.$request->reference,
                'provider_reference' => 'AZAMPAY-'.$request->reference,
                'source' => Payment::SOURCE_INTEGRATION,
            ]);

            $mno = $this->text($payload['mnoreference'] ?? null);
            $request->forceFill([
                'status' => PaymentRequest::SUCCEEDED,
                'failure_reason' => null,
                'payment_id' => $result['payment']->id,
                'provider_reference' => $request->provider_reference ?? ($externalRef !== '' ? $externalRef : null),
                'mno_reference' => $mno !== '' ? mb_substr($mno, 0, 100) : null,
                'completed_at' => now(),
            ])->save();

            return self::APPLIED;
        });
    }

    private function text(mixed $value): string
    {
        return is_string($value) ? $value : (is_int($value) ? (string) $value : '');
    }

    /** The paid amount in minor units (TZS), or null when it is not a plain amount. Never a float calculation. */
    private function minor(mixed $value): ?int
    {
        $text = match (true) {
            is_int($value) => (string) $value,
            is_float($value) => sprintf('%.2F', $value),
            is_string($value) => trim($value),
            default => '',
        };

        return Money::parse($text, 'TZS');
    }
}
