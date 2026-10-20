<?php

namespace App\Domain\Fees;

use App\Models\Invoice;
use App\Models\Payment;
use App\Support\Money;

/**
 * What an invoice has received and what is still owed, worked out from its payments each time.
 * Reversed payments do not count. Nothing about payment is stored on the invoice.
 */
final class InvoiceBalance
{
    public const UNPAID = 'unpaid';

    public const PARTIAL = 'partial';

    public const PAID = 'paid';

    public const OVERPAID = 'overpaid';

    public function __construct(
        public readonly int $total,
        public readonly int $paid,
        public readonly string $currency,
    ) {}

    public static function of(Invoice $invoice): self
    {
        $paid = (int) Payment::query()->where('invoice_id', $invoice->id)->whereDoesntHave('reversal')->sum('amount');

        return new self($invoice->total, $paid, $invoice->currency);
    }

    /** For a list: the invoice must have been loaded with `withSum` as `paid_sum`. */
    public static function fromLoaded(Invoice $invoice): self
    {
        return new self($invoice->total, (int) $invoice->getAttribute('paid_sum'), $invoice->currency);
    }

    /** What is still owed, in minor units; negative when more than the total was received. */
    public function balance(): int
    {
        return $this->total - $this->paid;
    }

    public function status(): string
    {
        return match (true) {
            $this->paid === 0 => self::UNPAID,
            $this->paid > $this->total => self::OVERPAID,
            $this->paid === $this->total => self::PAID,
            default => self::PARTIAL,
        };
    }

    public function paidMoney(): Money
    {
        return Money::of($this->paid, $this->currency);
    }

    public function balanceMoney(): Money
    {
        return Money::of($this->balance(), $this->currency);
    }
}
