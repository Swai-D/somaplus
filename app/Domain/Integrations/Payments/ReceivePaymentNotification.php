<?php

namespace App\Domain\Integrations\Payments;

use App\Domain\Fees\RecordPayment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentNotification;
use App\Support\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Applies a payment notice that a provider sent and whose signature was already verified. Runs inside the school's context.
 *
 * Expected body (all text): control_number (8 to 20 digits), amount (a decimal in the school's currency, for example
 * "50000.00"), currency, reference (the provider's own transaction id), and optionally paid_on (Y-m-d) and method.
 *
 * - Every notice with a valid reference is written to `payment_notifications`: applied, unmatched or rejected.
 * - The same reference again never records money twice. A notice that matched no invoice is kept and applied when the
 *   provider sends it again after the control number exists.
 * - Money that really moved is always recorded, even beyond the balance (the invoice then shows as overpaid).
 *   A different currency is refused and kept for a person to look at.
 */
final class ReceivePaymentNotification
{
    public const APPLIED = 'applied';

    public const DUPLICATE = 'duplicate';

    public const UNMATCHED = 'unmatched';

    public const REJECTED = 'rejected';

    public const INVALID = 'invalid';

    public function __construct(private readonly RecordPayment $record) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function handle(array $payload): string
    {
        $control = $payload['control_number'] ?? null;
        $reference = $payload['reference'] ?? null;
        $amountText = $payload['amount'] ?? null;
        $currency = $payload['currency'] ?? null;
        $method = $payload['method'] ?? 'bank';
        $paidOn = $payload['paid_on'] ?? CarbonImmutable::today()->toDateString();

        // Only text is believed: a JSON number could carry a float, and money is never a float.
        if (! is_string($control) || preg_match('/^\d{8,20}$/', $control) !== 1
            || ! is_string($reference) || trim($reference) === '' || mb_strlen($reference) > 100
            || ! is_string($amountText) || ! is_string($currency) || preg_match('/^[A-Za-z]{3}$/', $currency) !== 1
            || ! is_string($method) || ! in_array($method, Payment::METHODS, true)
            || ! is_string($paidOn) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $paidOn) !== 1) {
            return self::INVALID;
        }
        $reference = trim($reference);
        $currency = strtoupper($currency);

        $notification = $this->open($reference);
        if (in_array($notification->status, [self::APPLIED, self::REJECTED], true)) {
            return self::DUPLICATE;
        }

        $invoice = Invoice::query()->where('control_number', $control)->first();
        $amount = Money::parse($amountText, $currency);
        $date = CarbonImmutable::createFromFormat('!Y-m-d', $paidOn);

        $notification->forceFill(['control_number' => $control, 'amount' => $amount, 'currency' => $currency]);

        if ($invoice === null) {
            return $this->close($notification, self::UNMATCHED, 'unknown_control_number');
        }
        if ($amount === null || $amount <= 0 || $date === null || $date->isFuture()) {
            return $this->close($notification, self::REJECTED, 'bad_amount_or_date');
        }
        if ($invoice->currency !== $currency) {
            return $this->close($notification, self::REJECTED, 'currency_mismatch');
        }

        try {
            $result = $this->record->handle($invoice, [
                'amount' => $amount,
                'method' => $method,
                'paid_on' => $paidOn,
                'reference' => $reference,
                'idempotency_key' => 'provider:'.$reference,
                'provider_reference' => $reference,
                'source' => Payment::SOURCE_INTEGRATION,
            ]);
        } catch (ValidationException) {
            return $this->close($notification, self::REJECTED, 'conflicting_reference');
        }

        $notification->payment_id = $result['payment']->id;

        return $this->close($notification, self::APPLIED, null);
    }

    /** The notice already kept for this reference, or a new one. */
    private function open(string $reference): PaymentNotification
    {
        $existing = PaymentNotification::query()->where('provider_reference', $reference)->first();
        if ($existing !== null) {
            return $existing;
        }

        try {
            return DB::transaction(function () use ($reference): PaymentNotification {
                $notification = new PaymentNotification;
                $notification->forceFill(['provider_reference' => $reference, 'status' => self::UNMATCHED, 'received_at' => CarbonImmutable::now()])->save();

                return $notification;
            });
        } catch (UniqueConstraintViolationException) {
            return PaymentNotification::query()->where('provider_reference', $reference)->firstOrFail();
        }
    }

    private function close(PaymentNotification $notification, string $status, ?string $reason): string
    {
        $notification->forceFill(['status' => $status, 'reason' => $reason])->save();

        return $status;
    }
}
