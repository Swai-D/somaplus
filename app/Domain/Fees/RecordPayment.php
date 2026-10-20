<?php

namespace App\Domain\Fees;

use App\Domain\Settings\RecordAudit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Records money received for an invoice and gives it the next receipt number, in one transaction.
 *
 * - Idempotent: the same idempotency key (or the same provider reference) gives back the payment already
 *   recorded and never a second one; a key reused for a different invoice or amount is refused.
 * - The invoice row is locked while the balance is checked, so two payments cannot both pass the check.
 * - A person typing a payment cannot take the balance below zero. Money that a provider reports has already
 *   moved, so a payment from an integration is always recorded (it may show the invoice as overpaid).
 * - A payment never edits an invoice. Receipts are numbered without gaps (NextNumber, same transaction).
 */
final class RecordPayment
{
    public function __construct(private readonly NextNumber $numbers, private readonly RecordAudit $audit) {}

    /**
     * @param  array{amount: int, method: string, paid_on: string, idempotency_key: string, reference?: string|null, provider_reference?: string|null, source?: string}  $data  amount in minor units
     * @return array{payment: Payment, created: bool}
     */
    public function handle(Invoice $invoice, array $data): array
    {
        $key = trim($data['idempotency_key']);
        $source = $data['source'] ?? Payment::SOURCE_MANUAL;
        $providerRef = isset($data['provider_reference']) && trim((string) $data['provider_reference']) !== '' ? trim((string) $data['provider_reference']) : null;

        if ($key === '' || mb_strlen($key) > 100) {
            throw ValidationException::withMessages(['idempotency_key' => __('fees.payments.errors.key')]);
        }
        if ($data['amount'] <= 0) {
            throw ValidationException::withMessages(['amount' => __('fees.payments.errors.amount')]);
        }
        if (! in_array($data['method'], Payment::METHODS, true)) {
            throw ValidationException::withMessages(['method' => __('fees.payments.errors.method')]);
        }
        $paidOn = CarbonImmutable::createFromFormat('!Y-m-d', $data['paid_on']);
        if ($paidOn === null || $paidOn->isFuture()) {
            throw ValidationException::withMessages(['paid_on' => __('fees.payments.errors.date')]);
        }

        try {
            return DB::transaction(fn (): array => $this->record($invoice, $data, $key, $providerRef, $source, $paidOn));
        } catch (UniqueConstraintViolationException) {
            // A concurrent request with the same key or provider reference got there first: use its payment.
            $existing = $this->existing($key, $providerRef);
            if ($existing !== null) {
                return $this->same($existing, $invoice, $data['amount']);
            }

            throw ValidationException::withMessages(['idempotency_key' => __('fees.payments.errors.key')]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{payment: Payment, created: bool}
     */
    private function record(Invoice $invoice, array $data, string $key, ?string $providerRef, string $source, CarbonImmutable $paidOn): array
    {
        $locked = Invoice::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();

        $existing = $this->existing($key, $providerRef);
        if ($existing !== null) {
            return $this->same($existing, $locked, $data['amount']);
        }

        $balance = InvoiceBalance::of($locked)->balance();
        if ($source === Payment::SOURCE_MANUAL && $data['amount'] > $balance) {
            throw ValidationException::withMessages(['amount' => __('fees.payments.errors.too_much')]);
        }

        $payment = new Payment;
        $payment->forceFill([
            'invoice_id' => $locked->id,
            'amount' => $data['amount'],
            'currency' => $locked->currency,
            'method' => $data['method'],
            'source' => $source,
            'reference' => $this->clean($data['reference'] ?? null),
            'control_number' => $locked->control_number,
            'idempotency_key' => $key,
            'provider_reference' => $providerRef,
            'paid_on' => $paidOn,
            'recorded_by' => Auth::id(),
        ])->save();

        $receipt = new Receipt;
        $receipt->forceFill([
            'number' => $this->numbers->handle('receipt'),
            'payment_id' => $payment->id,
            'issued_on' => CarbonImmutable::today(),
        ])->save();

        $this->audit->handle('payment.recorded', $payment);

        return ['payment' => $payment, 'created' => true];
    }

    private function existing(string $key, ?string $providerRef): ?Payment
    {
        return Payment::query()
            ->where(fn ($q) => $q->where('idempotency_key', $key)->when($providerRef !== null, fn ($q) => $q->orWhere('provider_reference', $providerRef)))
            ->first();
    }

    /**
     * @return array{payment: Payment, created: bool}
     */
    private function same(Payment $existing, Invoice $invoice, int $amount): array
    {
        if ($existing->invoice_id !== $invoice->id || $existing->amount !== $amount) {
            throw ValidationException::withMessages(['idempotency_key' => __('fees.payments.errors.key_reused')]);
        }

        return ['payment' => $existing, 'created' => false];
    }

    private function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, 100);
    }
}
