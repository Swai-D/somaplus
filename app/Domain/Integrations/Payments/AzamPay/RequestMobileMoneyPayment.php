<?php

namespace App\Domain\Integrations\Payments\AzamPay;

use App\Domain\Fees\InvoiceBalance;
use App\Models\Invoice;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Sends a payment prompt to a payer's phone through the school's AzamPay app for one invoice. The request is saved
 * first (so a callback can always find it), then AzamPay is asked. The same idempotency key returns the first request,
 * so a double click never prompts the payer twice. The phone number is used to send the prompt and is not kept.
 * The payment itself is recorded only when AzamPay's signed callback confirms it (HandleAzamPayCallback).
 */
class RequestMobileMoneyPayment
{
    /**
     * @param  array{idempotency_key: string, provider: string, phone: string, amount: int}  $data  amount in minor units
     */
    public function handle(Invoice $invoice, array $data, ?User $user): PaymentRequest
    {
        $existing = PaymentRequest::query()->where('idempotency_key', $data['idempotency_key'])->first();
        if ($existing !== null) {
            return $existing;
        }

        $client = AzamPayClient::forCurrentTenant();
        if ($client === null || $invoice->currency !== 'TZS') {
            throw ValidationException::withMessages(['provider' => __('integrations.azampay.errors.not_available')]);
        }

        $phone = Msisdn::normalize($data['phone']);
        if ($phone === null) {
            throw ValidationException::withMessages(['phone' => __('integrations.azampay.errors.phone')]);
        }

        if (! in_array($data['provider'], (array) config('azampay.providers'), true)) {
            throw ValidationException::withMessages(['provider' => __('integrations.azampay.errors.provider')]);
        }

        $max = (int) config('azampay.max_amount') * 100;
        $balance = InvoiceBalance::of($invoice)->balance();
        if ($data['amount'] < 100 || $data['amount'] % 100 !== 0 || $data['amount'] > $max || $data['amount'] > $balance) {
            throw ValidationException::withMessages(['amount' => __('integrations.azampay.errors.amount', ['max' => Money::of(min($max, max($balance, 0)), 'TZS')->format()])]);
        }

        $request = new PaymentRequest;
        $request->forceFill([
            'invoice_id' => $invoice->id,
            'requested_by' => $user?->id,
            'idempotency_key' => $data['idempotency_key'],
            'reference' => 'SP'.strtoupper((string) Str::ulid()),
            'driver' => 'azampay',
            'provider' => $data['provider'],
            'amount' => $data['amount'],
            'currency' => 'TZS',
            'status' => PaymentRequest::PENDING,
            'expires_at' => now()->addMinutes((int) config('azampay.request_ttl_minutes')),
        ])->save();

        try {
            $result = $client->checkout($phone, intdiv($data['amount'], 100), 'TZS', $request->reference, $data['provider']);
        } catch (AzamPayException $e) {
            // Only the reason is logged: never the phone number or a credential.
            report($e);
            // The prompt may or may not have left; a late successful callback can still record the payment.
            $request->forceFill(['status' => PaymentRequest::FAILED, 'failure_reason' => 'unreachable', 'completed_at' => now()])->save();

            return $request;
        }

        if (! $result['success']) {
            $request->forceFill(['status' => PaymentRequest::FAILED, 'failure_reason' => mb_substr((string) ($result['message'] ?? 'rejected'), 0, 250), 'completed_at' => now()])->save();

            return $request;
        }

        $request->forceFill(['provider_reference' => $result['transactionId']])->save();

        return $request;
    }
}
