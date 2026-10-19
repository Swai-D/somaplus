<?php

namespace App\Domain\Attendance;

use App\Domain\Settings\RecordAudit;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Stream;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Saves the register of one class (and stream) for one day. There is one row per
 * student and day, so sending the same register again, or correcting it, updates
 * those rows and never adds duplicates. Only students who are in that class on
 * that day can be marked.
 */
final class RecordAttendance
{
    public function __construct(private readonly ClassRoll $roll, private readonly RecordAudit $audit) {}

    /**
     * @param  array<int|string, array{status: string, note?: string|null}>  $rows  keyed by student id
     */
    public function handle(SchoolClass $class, ?Stream $stream, string $date, array $rows): int
    {
        $year = $this->roll->yearOn($date);
        if ($year === null) {
            throw ValidationException::withMessages(['date' => __('attendance.errors.no_year')]);
        }

        $students = $this->roll->students($class, $stream, $year)->keyBy('id');

        return DB::transaction(function () use ($class, $stream, $date, $rows, $year, $students): int {
            foreach ($rows as $studentId => $row) {
                $student = $students->get((int) $studentId);
                if ($student === null) {
                    throw ValidationException::withMessages(['rows' => __('attendance.errors.not_in_class')]);
                }

                $note = trim((string) ($row['note'] ?? ''));

                // whereDate, because a date cast may be stored with a time part on some databases.
                $record = AttendanceRecord::query()
                    ->where('student_id', $student->id)
                    ->whereDate('attended_on', $date)
                    ->first() ?? new AttendanceRecord(['student_id' => $student->id, 'attended_on' => $date]);

                $record->fill([
                    'academic_year_id' => $year->id,
                    'school_class_id' => $class->id,
                    'stream_id' => $stream?->id,
                    'status' => $row['status'],
                    'note' => $note === '' ? null : $note,
                    'recorded_by' => Auth::id(),
                ])->save();
            }

            $this->audit->handle('attendance.recorded', $class);

            return count($rows);
        });
    }
}
