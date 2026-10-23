<?php

namespace App\Domain\Timetable;

use App\Models\Term;
use App\Models\TimetableEntry;
use Illuminate\Validation\ValidationException;

/**
 * Copies the lessons of one term into another, so a school does not type its timetable again every
 * term. Each lesson goes through SaveTimetableEntry, so it meets the same rules; a lesson that cannot
 * be placed (a clash with what the target term already has, a teacher now out of duty) is skipped and
 * counted. Running it twice therefore adds nothing the second time.
 */
final class CopyTimetable
{
    public function __construct(private readonly SaveTimetableEntry $save) {}

    /**
     * @return array{copied: int, skipped: int}
     */
    public function handle(Term $from, Term $to): array
    {
        if ($from->is($to)) {
            throw ValidationException::withMessages(['to_term_id' => __('timetable.errors.same_term')]);
        }

        $copied = 0;
        $skipped = 0;

        foreach (TimetableEntry::query()->where('term_id', $from->id)->orderBy('id')->get() as $lesson) {
            try {
                $this->save->handle([
                    'term_id' => $to->id,
                    'school_class_id' => $lesson->school_class_id,
                    'stream_id' => $lesson->stream_id,
                    'subject_id' => $lesson->subject_id,
                    'teacher_id' => $lesson->teacher_id,
                    'timetable_period_id' => $lesson->timetable_period_id,
                    'weekday' => $lesson->weekday,
                    'room' => $lesson->room,
                ]);
                $copied++;
            } catch (ValidationException) {
                $skipped++;
            }
        }

        return ['copied' => $copied, 'skipped' => $skipped];
    }
}
