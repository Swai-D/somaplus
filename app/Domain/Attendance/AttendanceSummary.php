<?php

namespace App\Domain\Attendance;

use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;

/**
 * Counts of each status per student for a class (and stream) between two dates.
 * Late counts as attended in the rate; excused counts as neither way (left out of it).
 */
final class AttendanceSummary
{
    /**
     * @return list<array{student: Student, present: int, absent: int, late: int, excused: int, days: int, rate: int|null}>
     */
    public function handle(SchoolClass $class, ?Stream $stream, string $from, string $to): array
    {
        $records = AttendanceRecord::query()
            ->with('student')
            ->where('school_class_id', $class->id)
            ->when($stream !== null, fn ($q) => $q->where('stream_id', $stream?->id))
            ->whereDate('attended_on', '>=', $from)
            ->whereDate('attended_on', '<=', $to)
            ->get();

        $rows = [];
        foreach ($records as $record) {
            $rows[$record->student_id] ??= [
                'student' => $record->student, 'present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0, 'days' => 0, 'rate' => null,
            ];
            $rows[$record->student_id][$record->status]++;
            $rows[$record->student_id]['days']++;
        }

        foreach ($rows as &$row) {
            $counted = $row['present'] + $row['late'] + $row['absent'];
            $row['rate'] = $counted === 0 ? null : (int) round(100 * ($row['present'] + $row['late']) / $counted);
        }
        unset($row);

        $list = array_values($rows);
        usort($list, fn (array $a, array $b): int => [$a['student']->first_name, $a['student']->last_name] <=> [$b['student']->first_name, $b['student']->last_name]);

        return $list;
    }
}
