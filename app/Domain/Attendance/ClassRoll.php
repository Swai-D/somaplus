<?php

namespace App\Domain\Attendance;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

/**
 * The students who are in a class (and stream) in an academic year: those whose
 * newest placement of that year is there, and who have not left school.
 */
final class ClassRoll
{
    public function yearOn(string $date): ?AcademicYear
    {
        return AcademicYear::query()->whereDate('starts_on', '<=', $date)->whereDate('ends_on', '>=', $date)->first();
    }

    /**
     * @return Collection<int, Student>
     */
    public function students(SchoolClass $class, ?Stream $stream, AcademicYear $year): Collection
    {
        $latest = Enrollment::query()->selectRaw('max(id)')->where('academic_year_id', $year->id)->groupBy('student_id');

        $placed = Enrollment::query()
            ->select('student_id')
            ->where('academic_year_id', $year->id)
            ->where('school_class_id', $class->id)
            ->when($stream !== null, fn ($q) => $q->where('stream_id', $stream?->id))
            ->whereIn('id', $latest);

        return Student::query()
            ->where('status', Student::ACTIVE)
            ->whereIn('id', $placed)
            ->orderBy('first_name')->orderBy('last_name')->orderBy('id')
            ->get();
    }
}
