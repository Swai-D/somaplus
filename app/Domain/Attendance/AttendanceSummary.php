<?php

namespace App\Domain\Attendance;

use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Stream;

/**
 * Counts of each status per student for a class (and stream) between two dates.
 */
final class AttendanceSummary
{
    /**
     * @return list<StudentAttendance>
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

        $perStudent = [];
        foreach ($records as $record) {
            $perStudent[$record->student_id] ??= new StudentAttendance($record->student);
            $perStudent[$record->student_id]->add($record->status);
        }

        $list = array_values($perStudent);
        usort($list, fn (StudentAttendance $a, StudentAttendance $b): int => [$a->student->first_name, $a->student->last_name] <=> [$b->student->first_name, $b->student->last_name]);

        return $list;
    }
}
