<?php

namespace App\Domain\Settings;

use App\Domain\Exams\TopStudents;
use App\Domain\Fees\InvoiceBalance;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\Exam;
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
 * guards its page is missing (students.view, academics.view, fees.view, attendance.manage,
 * results.view). Everything is read through the tenant scope, so a school never sees another
 * school's figures.
 */
final class SchoolDashboardFigures
{
    /** The roles that teach in the school. */
    private const TEACHER_ROLES = ['teacher', 'head_teacher'];

    /** The roles that run the school. */
    private const STAFF_ROLES = ['owner', 'accountant'];

    public function __construct(private readonly TopStudents $topStudents) {}

    /**
     * @return array{
     *     students: array{active: int, new_this_month: int}|null,
     *     guardians: int|null,
     *     classes: int|null,
     *     people: array{students: int, teachers: int, staff: int}|null,
     *     class_breakdown: list<array{name: string, count: int, share: int}>|null,
     *     fees: array{
     *         year: string,
     *         currency: string,
     *         invoiced: Money,
     *         collected: Money,
     *         outstanding: Money,
     *         chart: array{labels: list<string>, invoiced: list<int>, collected: list<int>},
     *     }|null,
     *     attendance: array{total: int, rows: list<array{status: string, count: int, percent: int}>}|null,
     *     attendance_trend: array{labels: list<string>, present: list<int>, absent: list<int>}|null,
     *     recent_students: list<Student>|null,
     *     recent_payments: list<Payment>|null,
     *     top_students: array{exam: Exam|null, rows: list<array{student: Student, class: string, average: int, position: int|null}>}|null,
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
            'people' => $canStudents ? $this->people() : null,
            'class_breakdown' => $canStudents ? $this->classBreakdown($year) : null,
            'fees' => $canFees && $year !== null ? $this->fees($year, $school->currency) : null,
            'attendance' => $user->can('attendance.manage') ? $this->attendanceToday() : null,
            'attendance_trend' => $user->can('attendance.manage') ? $this->attendanceTrend() : null,
            'recent_students' => $canStudents ? $this->recentStudents() : null,
            'recent_payments' => $canFees ? $this->recentPayments() : null,
            'top_students' => $user->can('results.view') ? $this->topStudents->handle() : null,
        ];
    }

    /**
     * @return array{active: int, new_this_month: int}
     */
    public function students(): array
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
    public function attendanceToday(): array
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
     * The people of the school: the pupils, the accounts that teach and the accounts that run it.
     * The school has no staff records yet, so a teacher or a staff member is an account with that
     * role; the pupil count is the number of students who are still at school.
     *
     * @return array{students: int, teachers: int, staff: int}
     */
    private function people(): array
    {
        return [
            'students' => Student::query()->where('status', Student::ACTIVE)->count(),
            'teachers' => $this->usersWithRoles(self::TEACHER_ROLES),
            'staff' => $this->usersWithRoles(self::STAFF_ROLES),
        ];
    }

    private function usersWithRoles(array $roles): int
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', $roles))
            ->count();
    }

    /**
     * How many pupils each class holds in the current academic year. The placement that counts
     * is the newest one of the year, so a pupil who moved is counted once, in the class they are
     * in now.
     *
     * @return list<array{name: string, count: int, share: int}>
     */
    private function classBreakdown(?AcademicYear $year): array
    {
        if ($year === null) {
            return [];
        }

        $students = Student::query()->where('status', Student::ACTIVE)->get(['id']);

        // The newest placement of each pupil in the year is the one that counts.
        $placements = [];
        Enrollment::query()
            ->with('schoolClass')
            ->where('academic_year_id', $year->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->orderBy('id')
            ->get()
            ->each(function (Enrollment $enrollment) use (&$placements): void {
                $placements[$enrollment->student_id] = $enrollment;
            });

        $counts = [];
        foreach ($placements as $placement) {
            $counts[$placement->school_class_id] = ($counts[$placement->school_class_id] ?? 0) + 1;
        }

        $total = max(1, count($placements));
        $classes = SchoolClass::query()->whereIn('id', array_keys($counts))->get()->keyBy('id');

        $rows = [];
        foreach ($counts as $classId => $count) {
            $class = $classes->get($classId);

            $rows[] = [
                'name' => $class instanceof SchoolClass ? $class->name : '',
                'count' => $count,
                'share' => (int) round($count * 100 / $total),
            ];
        }

        usort($rows, fn (array $a, array $b): int => [$b['count'], $a['name']] <=> [$a['count'], $b['name']]);

        return $rows;
    }

    /**
     * The register of the last thirty days, day by day, so the trend can be drawn.
     *
     * @return array{labels: list<string>, present: list<int>, absent: list<int>}
     */
    private function attendanceTrend(): array
    {
        $today = CarbonImmutable::today();
        $from = $today->subDays(29);

        $records = AttendanceRecord::query()
            ->whereDate('attended_on', '>=', $from->toDateString())
            ->whereDate('attended_on', '<=', $today->toDateString())
            ->get(['attended_on', 'status']);

        $perDay = [];
        foreach ($records as $record) {
            $day = $record->attended_on->format('Y-m-d');
            $perDay[$day] ??= ['present' => 0, 'absent' => 0];
            if ($record->status === AttendanceRecord::PRESENT) {
                $perDay[$day]['present']++;
            } elseif ($record->status === AttendanceRecord::ABSENT) {
                $perDay[$day]['absent']++;
            }
        }

        $labels = [];
        $present = [];
        $absent = [];
        for ($day = $from; $day <= $today; $day = $day->addDay()) {
            $key = $day->format('Y-m-d');
            $counts = $perDay[$key] ?? ['present' => 0, 'absent' => 0];

            $labels[] = $day->format('j M');
            $present[] = $counts['present'];
            $absent[] = $counts['absent'];
        }

        return ['labels' => $labels, 'present' => $present, 'absent' => $absent];
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
