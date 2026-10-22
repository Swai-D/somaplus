<?php

namespace App\Domain\Settings;

use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;

/**
 * The numbers on a teacher's dashboard. Only real data. Beside what this person did
 * themselves (the marks they entered) and the exams of the current academic year, the
 * page shows what the school assigned them under Teachers, and — for the people who
 * may see the library — how its copies stand today.
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
     *     teaches: array{subjects: list<string>, classes: list<string>}|null,
     *     library: array{copies: int, shelf: int, out: int, overdue: int}|null,
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
            'teaches' => $this->teaches($user),
            'library' => $user->can('library.view') ? $this->library() : null,
        ];
    }

    /**
     * What the school assigned this person under Teachers, empty when they hold no
     * teacher record (the owner and the head teacher usually do not).
     *
     * @return array{subjects: list<string>, classes: list<string>}|null
     */
    private function teaches(User $user): ?array
    {
        $teacher = Teacher::query()->where('user_id', $user->id)->first();
        if ($teacher === null) {
            return null;
        }

        return [
            'subjects' => $teacher->subjects()->orderBy('name')->pluck('name')->all(),
            'classes' => $teacher->classes()->orderBy('name')->pluck('name')->all(),
        ];
    }

    /**
     * How the copies of the library stand: the copies the school owns, those on the
     * shelf and the open ones whose return day has passed.
     *
     * @return array{copies: int, shelf: int, out: int, overdue: int}
     */
    private function library(): array
    {
        $copies = (int) Book::query()->sum('quantity');
        $out = (int) BookIssue::query()->whereNull('returned_on')->count();
        $overdue = (int) BookIssue::query()
            ->whereNull('returned_on')
            ->where('due_on', '<', now()->toDateString())
            ->count();

        return [
            'copies' => $copies,
            'shelf' => $copies - $out,
            'out' => $out,
            'overdue' => $overdue,
        ];
    }
}
