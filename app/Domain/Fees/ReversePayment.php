<?php

namespace App\Domain\Fees;

use App\Domain\Settings\RecordAudit;
use App\Models\Payment;
use App\Models\PaymentReversal;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Undoes a payment that was recorded by mistake, with a reason. The payment and its receipt stay as they are
 * (receipt numbers never gain a gap); the balance of the invoice simply stops counting the payment.
 * Reversing twice is harmless: the first reversal stands.
 */
final class ReversePayment
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Payment $payment, string $reason): PaymentReversal
    {
        $reason = trim($reason);
        if (mb_strlen($reason) < 3 || mb_strlen($reason) > 250) {
            throw ValidationException::withMessages(['reason' => __('fees.payments.errors.reason')]);
        }

        return DB::transaction(function () use ($payment, $reason): PaymentReversal {
            // Locks the invoice first, like RecordPayment, so balances are never read half way through a change.
            $payment = Payment::query()->whereKey($payment->id)->firstOrFail();
            $payment->invoice()->lockForUpdate()->firstOrFail();

            $existing = PaymentReversal::query()->where('payment_id', $payment->id)->first();
            if ($existing !== null) {
                return $existing;
            }

            $reversal = new PaymentReversal;
            $reversal->forceFill([
                'payment_id' => $payment->id,
                'reason' => $reason,
                'reversed_by' => Auth::id(),
                'reversed_on' => CarbonImmutable::today(),
            ])->save();

            $this->audit->handle('payment.reversed', $payment);

            return $reversal;
        });
    }
}
