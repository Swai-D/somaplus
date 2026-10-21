<?php

namespace App\Domain\Fees;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentNotification;
use Carbon\CarbonImmutable;

/**
 * Money collected by the school between two dates (by the date paid, both days included), worked out from the payments
 * themselves: a reversed payment is not counted (it is listed apart), and nothing is stored or edited. It also lists the
 * provider notices that were NOT recorded (no invoice had the control number, or the notice was refused), so that money
 * a bank or provider says it sent can be followed up, and what is still owed on invoices today.
 *
 * Everything is grouped by currency, in minor units, so nothing is ever added across currencies or as a float.
 * It holds no student or guardian data, so it needs only `fees.view` and is not written to the audit log.
 */
final class CollectionsReport
{
    public const NOTICE_LIMIT = 50;

    /**
     * @return array{
     *     collected: array<string, array{total: int, count: int, methods: array<string, int>, sources: array<string, int>}>,
     *     days: array<string, array<string, int>>,
     *     reversed: array<string, array{total: int, count: int}>,
     *     outstanding: array<string, array{total: int, invoices: int}>,
     *     notices: list<PaymentNotification>,
     *     notice_counts: array<string, int>
     * }
     */
    public function handle(CarbonImmutable $from, CarbonImmutable $to): array
    {
        // whereDate, not whereBetween on the raw value: the column may hold a time part, and both end days must be counted.
        $inRange = fn () => Payment::query()->whereDate('paid_on', '>=', $from->toDateString())->whereDate('paid_on', '<=', $to->toDateString());

        $collected = [];
        $days = [];
        foreach ($inRange()->whereDoesntHave('reversal')->orderBy('paid_on')->get(['id', 'amount', 'currency', 'method', 'source', 'paid_on']) as $payment) {
            $row = &$collected[$payment->currency];
            $row ??= ['total' => 0, 'count' => 0, 'methods' => [], 'sources' => []];
            $row['total'] += $payment->amount;
            $row['count']++;
            $row['methods'][$payment->method] = ($row['methods'][$payment->method] ?? 0) + $payment->amount;
            $row['sources'][$payment->source] = ($row['sources'][$payment->source] ?? 0) + $payment->amount;
            unset($row);

            $day = $payment->paid_on->toDateString();
            $days[$day][$payment->currency] = ($days[$day][$payment->currency] ?? 0) + $payment->amount;
        }

        $reversed = [];
        foreach ($inRange()->whereHas('reversal')->get(['id', 'amount', 'currency']) as $payment) {
            $reversed[$payment->currency]['total'] = ($reversed[$payment->currency]['total'] ?? 0) + $payment->amount;
            $reversed[$payment->currency]['count'] = ($reversed[$payment->currency]['count'] ?? 0) + 1;
        }

        $outstanding = [];
        $invoices = Invoice::query()
            ->withSum(['payments as paid_sum' => fn ($query) => $query->whereDoesntHave('reversal')], 'amount')
            ->get(['id', 'total', 'currency']);
        foreach ($invoices as $invoice) {
            $owed = $invoice->total - (int) $invoice->getAttribute('paid_sum');
            if ($owed > 0) {
                $outstanding[$invoice->currency]['total'] = ($outstanding[$invoice->currency]['total'] ?? 0) + $owed;
                $outstanding[$invoice->currency]['invoices'] = ($outstanding[$invoice->currency]['invoices'] ?? 0) + 1;
            }
        }

        $notices = PaymentNotification::query()
            ->whereIn('status', ['unmatched', 'rejected'])
            ->whereBetween('received_at', [$from->startOfDay(), $to->endOfDay()]);
        $noticeCounts = (clone $notices)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->map(fn ($count): int => (int) $count)->all();

        ksort($days);

        return [
            'collected' => $collected,
            'days' => $days,
            'reversed' => $reversed,
            'outstanding' => $outstanding,
            'notices' => $notices->orderByDesc('id')->limit(self::NOTICE_LIMIT)->get()->all(),
            'notice_counts' => $noticeCounts,
        ];
    }
}
