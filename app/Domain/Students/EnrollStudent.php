<?php

namespace App\Domain\Students;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Validation\ValidationException;

/**
 * Places a student in a class (and stream) for an academic year by adding a row.
 * A promotion or a move never changes an earlier row. Sending the same
 * placement again (same year, class and stream as the newest one) adds nothing.
 */
final class EnrollStudent
{
    /**
     * @param  array{academic_year_id: int|string, school_class_id: int|string, stream_id?: int|string|null, enrolled_on: string}  $data
     */
    public function handle(Student $student, array $data): Enrollment
    {
        if (! $student->isActive()) {
            throw ValidationException::withMessages(['school_class_id' => __('students.errors.left')]);
        }

        $year = (int) $data['academic_year_id'];
        $class = (int) $data['school_class_id'];
        $stream = isset($data['stream_id']) && $data['stream_id'] !== '' ? (int) $data['stream_id'] : null;

        $latest = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('academic_year_id', $year)
            ->orderByDesc('id')
            ->first();

        if ($latest !== null && $latest->school_class_id === $class && $latest->stream_id === $stream) {
            return $latest;
        }

        $enrollment = new Enrollment([
            'academic_year_id' => $year,
            'school_class_id' => $class,
            'stream_id' => $stream,
            'enrolled_on' => $data['enrolled_on'],
        ]);
        $enrollment->student_id = $student->id;
        $enrollment->save();

        return $enrollment;
    }
}
