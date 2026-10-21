<?php

namespace App\Domain\Settings;

use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\User;

/**
 * The numbers on a teacher's dashboard. Only real data. The application does not yet record which classes
 * or subjects a teacher takes, so the school's own counts are shown beside what this person did themselves
 * (marks they entered) and the exams of the current academic year.
 */
final class TeacherDashboardFigures
{
    public function __construct(private readonly SchoolDashboardFigures $school) {}

    /**
     * @return array{
     *     students: int,
     *     classes: int,
     *     marks_entered: int,
     *     open_exams: int,
     *     attendance: array{total: int, rows: list<array{status: string, count: int, percent: int}>}|null,
     *     exams: list<Exam>,
     * }
     */
    public function handle(User $user): array
    {
        $year = AcademicYear::query()->where('is_current', true)->first();
        $exams = $year === null ? collect() : Exam::query()
            ->where('academic_year_id', $year->id)
            ->with('term')
            ->withCount(['marks as my_marks_count' => fn ($marks) => $marks->where('entered_by', $user->id)])
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return [
            'students' => $this->school->students()['active'],
            'classes' => SchoolClass::query()->count(),
            'marks_entered' => ExamMark::query()->where('entered_by', $user->id)->count(),
            'open_exams' => $year === null ? 0 : Exam::query()->where('academic_year_id', $year->id)->whereNull('locked_at')->count(),
            'attendance' => $user->can('attendance.manage') ? $this->school->attendanceToday() : null,
            'exams' => $exams->values()->all(),
        ];
    }
}
