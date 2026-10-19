<?php

namespace App\Domain\Students;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Student;

/**
 * The placement that counts for each student in an academic year: the newest row.
 */
final class CurrentPlacements
{
    /**
     * @param  iterable<Student>  $students
     * @return array<int, Enrollment> keyed by student id
     */
    public function for(iterable $students, ?AcademicYear $year): array
    {
        $ids = [];
        foreach ($students as $student) {
            $ids[] = $student->id;
        }

        if ($year === null || $ids === []) {
            return [];
        }

        $placements = [];
        $rows = Enrollment::query()
            ->with(['schoolClass', 'stream'])
            ->where('academic_year_id', $year->id)
            ->whereIn('student_id', $ids)
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            $placements[$row->student_id] = $row;
        }

        return $placements;
    }
}
