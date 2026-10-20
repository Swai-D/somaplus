<?php

namespace App\Domain\Fees;

use App\Domain\Settings\RecordAudit;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Sets or clears the control number of an invoice: the reference a bank or mobile-money channel gives
 * for paying it. It is 8 to 20 digits and belongs to one invoice of the school, so a payment made
 * with it can be matched to exactly one invoice.
 */
final class SetControlNumber
{
    public function __construct(private readonly RecordAudit $audit) {}

    public static function normalise(?string $input): ?string
    {
        $input = trim((string) $input);

        return $input === '' ? null : $input;
    }

    public function handle(Invoice $invoice, ?string $controlNumber): Invoice
    {
        $controlNumber = self::normalise($controlNumber);

        if ($controlNumber !== null && preg_match('/^\d{8,20}$/', $controlNumber) !== 1) {
            throw ValidationException::withMessages(['control_number' => __('fees.invoices.errors.control_number')]);
        }

        return DB::transaction(function () use ($invoice, $controlNumber): Invoice {
            $fresh = Invoice::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if ($controlNumber !== null && Invoice::query()->where('control_number', $controlNumber)->whereKeyNot($fresh->id)->exists()) {
                throw ValidationException::withMessages(['control_number' => __('fees.invoices.errors.control_taken')]);
            }

            $fresh->forceFill(['control_number' => $controlNumber])->save();
            $this->audit->handle('invoice.control_number_set', $fresh);

            return $fresh;
        });
    }
}
