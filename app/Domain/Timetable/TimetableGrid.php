<?php

namespace App\Domain\Timetable;

use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\TimetableEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Reads a term's timetable as a week: the lessons of a class (and stream), of a teacher or of a
 * student, grouped by weekday and ordered by the time of their period. Monday to Friday are always
 * listed; Saturday and Sunday only when they hold a lesson. Read only: it never changes anything.
 *
 * @phpstan-type Week array{days: list<int>, week: array<int, Collection<int, TimetableEntry>>}
 */
final class TimetableGrid
{
    /**
     * A class's lessons. With a stream, the lessons of that stream plus those given to the whole class.
     *
     * @return Week
     */
    public function forClass(Term $term, SchoolClass $class, ?Stream $stream): array
    {
        return $this->arrange($this->base($term)
            ->where('school_class_id', $class->id)
            ->when($stream !== null, fn (Builder $q) => $q->where(fn (Builder $w) => $w->whereNull('stream_id')->orWhere('stream_id', $stream?->id)))
            ->get());
    }

    /**
     * @return Week
     */
    public function forTeacher(Term $term, Teacher $teacher): array
    {
        return $this->arrange($this->base($term)->where('teacher_id', $teacher->id)->get());
    }

    /**
     * The lessons of a student: those of the class and stream of their newest placement in the term's year.
     *
     * @return array{days: list<int>, week: array<int, Collection<int, TimetableEntry>>, class: SchoolClass|null, stream: Stream|null}
     */
    public function forStudent(Term $term, Student $student): array
    {
        $placement = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('academic_year_id', $term->academic_year_id)
            ->orderByDesc('id')
            ->first();

        $class = $placement === null ? null : SchoolClass::query()->find($placement->school_class_id);
        $stream = $placement?->stream_id === null ? null : Stream::query()->find($placement->stream_id);

        $week = $class === null ? $this->arrange(collect()) : $this->forClass($term, $class, $stream);

        return $week + ['class' => $class, 'stream' => $stream];
    }

    /**
     * @return Builder<TimetableEntry>
     */
    private function base(Term $term): Builder
    {
        return TimetableEntry::query()
            ->where('term_id', $term->id)
            ->with(['period', 'subject', 'teacher.user', 'schoolClass', 'stream']);
    }

    /**
     * @param  Collection<int, TimetableEntry>  $entries
     * @return Week
     */
    private function arrange(Collection $entries): array
    {
        $sorted = $entries
            ->sortBy(fn (TimetableEntry $e): string => $e->period->starts_at.'|'.str_pad((string) $e->id, 12, '0', STR_PAD_LEFT))
            ->values();

        $days = array_values(array_unique(array_merge(TimetableEntry::SCHOOL_DAYS, $sorted->pluck('weekday')->map(fn ($d): int => (int) $d)->all())));
        sort($days);

        return ['days' => $days, 'week' => $sorted->groupBy('weekday')->all()];
    }
}
