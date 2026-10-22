<?php

namespace App\Domain\Communication;

use App\Domain\Fees\InvoiceBalance;
use App\Models\Invoice;
use App\Models\Tenant;

/** The fee reminder text for one invoice, in the chosen language. Worked out when sent, never stored. */
final class ReminderMessage
{
    public static function text(Invoice $invoice, Tenant $school, string $locale): string
    {
        $balance = InvoiceBalance::of($invoice)->balanceMoney()->format();
        $control = $invoice->control_number !== null ? __('communication.reminder_control', ['number' => $invoice->control_number], $locale) : '';

        return trim(__('communication.reminder', [
            'school' => $school->name,
            'student' => $invoice->student->fullName(),
            'balance' => $balance,
            'invoice' => $invoice->reference(),
            'control' => $control,
        ], $locale));
    }
}
