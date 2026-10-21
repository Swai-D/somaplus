<?php

namespace App\Domain\Settings;

use App\Domain\Fees\InvoiceBalance;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Money;
use Carbon\CarbonImmutable;

/**
 * The numbers, chart series and short lists on a school's dashboard. Only real data, and only what
 * the person may see anywhere else in the application: a block is `null` when the permission that
 * guards its page is missing (students.view, academics.view, fees.view, attendance.manage).
 * Everything is read through the tenant scope, so a school never sees another school's figures.
 */
final class SchoolDashboardFigures
{
    /**
     * @return array{
     *     students: array{active: int, new_this_month: int}|null,
     *     guardians: int|null,
     *     classes: int|null,
     *     fees: array{
     *         year: string,
     *         currency: string,
     *         invoiced: Money,
     *         collected: Money,
     *         outstanding: Money,
     *         chart: array{labels: list<string>, invoiced: list<int>, collected: list<int>},
     *     }|null,
     *     attendance: array{total: int, rows: list<array{status: string, count: int, percent: int}>}|null,
     *     recent_students: list<Student>|null,
     *     recent_payments: list<Payment>|null,
     * }
     */
    public function handle(User $user, Tenant $school, ?AcademicYear $year): array
    {
        $canStudents = $user->can('students.view');
        $canFees = $user->can('fees.view');

        return [
            'students' => $canStudents ? $this->students() : null,
            'guardians' => $canStudents ? Guardian::query()->count() : null,
            'classes' => $user->can('academics.view') ? SchoolClass::query()->count() : null,
            'fees' => $canFees && $year !== null ? $this->fees($year, $school->currency) : null,
            'attendance' => $user->can('attendance.manage') ? $this->attendanceToday() : null,
            'recent_students' => $canStudents ? $this->recentStudents() : null,
            'recent_payments' => $canFees ? $this->recentPayments() : null,
        ];
    }

    /**
     * @return array{active: int, new_this_month: int}
     */
    private function students(): array
    {
        $active = Student::query()->where('status', Student::ACTIVE);

        return [
            'active' => (clone $active)->count(),
            'new_this_month' => (clone $active)
                ->whereDate('admitted_on', '>=', today()->startOfMonth()->toDateString())
                ->count(),
        ];
    }

    /**
     * Invoiced, received and still owed for the school's current academic year, and the same by month
     * (twelve months from the month the year starts). Reversed payments do not count, and an invoice
     * that was overpaid does not reduce what other invoices owe.
     *
     * @return array{
     *     year: string,
     *     currency: string,
     *     invoiced: Money,
     *     collected: Money,
     *     outstanding: Money,
     *     chart: array{labels: list<string>, invoiced: list<int>, collected: list<int>},
     * }
     */
    private function fees(AcademicYear $year, string $currency): array
    {
        $ofYear = fn ($query) => $query->where('academic_year_id', $year->id);

        $invoicedMinor = 0;
        $outstandingMinor = 0;
        $invoices = Invoice::query()
            ->where('academic_year_id', $year->id)
            ->withSum(['payments as paid_sum' => fn ($q) => $q->whereDoesntHave('reversal')], 'amount')
            ->cursor();

        foreach ($invoices as $invoice) {
            $invoicedMinor += $invoice->total;
            $outstandingMinor += max(0, InvoiceBalance::fromLoaded($invoice)->balance());
        }

        $collectedMinor = (int) Payment::query()
            ->whereHas('invoice', $ofYear)
            ->whereDoesntHave('reversal')
            ->sum('amount');

        $months = [];
        $first = CarbonImmutable::parse($year->starts_on)->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $month = $first->addMonthsNoOverflow($i);
            $months[$month->format('Y-m')] = ['label' => $month->translatedFormat('M'), 'invoiced' => 0, 'collected' => 0];
        }

        $issued = Invoice::query()
            ->where('academic_year_id', $year->id)
            ->selectRaw('issued_on, sum(total) as amount')
            ->groupBy('issued_on')
            ->get();
        foreach ($issued as $row) {
            $key = $row->issued_on->format('Y-m');
            if (isset($months[$key])) {
                $months[$key]['invoiced'] += (int) $row->getAttribute('amount');
            }
        }

        $received = Payment::query()
            ->whereHas('invoice', $ofYear)
            ->whereDoesntHave('reversal')
            ->selectRaw('paid_on, sum(amount) as total')
            ->groupBy('paid_on')
            ->get();
        foreach ($received as $row) {
            $key = $row->paid_on->format('Y-m');
            if (isset($months[$key])) {
                $months[$key]['collected'] += (int) $row->getAttribute('total');
            }
        }

        // The chart is drawn in whole currency units; the minor units stay in the cards above it.
        $divisor = (int) (10 ** Money::CURRENCIES[$currency]);

        return [
            'year' => $year->name,
            'currency' => $currency,
            'invoiced' => Money::of($invoicedMinor, $currency),
            'collected' => Money::of($collectedMinor, $currency),
            'outstanding' => Money::of($outstandingMinor, $currency),
            'chart' => [
                'labels' => array_values(array_map(fn (array $m): string => $m['label'], $months)),
                'invoiced' => array_values(array_map(fn (array $m): int => intdiv($m['invoiced'], $divisor), $months)),
                'collected' => array_values(array_map(fn (array $m): int => intdiv($m['collected'], $divisor), $months)),
            ],
        ];
    }

    /**
     * Today's register across the whole school, one row per status in the order the register uses.
     *
     * @return array{total: int, rows: list<array{status: string, count: int, percent: int}>}
     */
    private function attendanceToday(): array
    {
        $counts = AttendanceRecord::query()
            ->whereDate('attended_on', today()->toDateString())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $total = (int) $counts->sum();
        $rows = [];

        foreach (AttendanceRecord::STATUSES as $status) {
            $count = (int) ($counts[$status] ?? 0);
            $rows[] = [
                'status' => $status,
                'count' => $count,
                'percent' => $total > 0 ? (int) round($count * 100 / $total) : 0,
            ];
        }

        return ['total' => $total, 'rows' => $rows];
    }

    /**
     * @return list<Student>
     */
    private function recentStudents(): array
    {
        return Student::query()
            ->where('status', Student::ACTIVE)
            ->orderByDesc('admitted_on')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->values()
            ->all();
    }

    /**
     * @return list<Payment>
     */
    private function recentPayments(): array
    {
        return Payment::query()
            ->whereDoesntHave('reversal')
            ->with('invoice.student')
            ->orderByDesc('paid_on')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->values()
            ->all();
    }
}
