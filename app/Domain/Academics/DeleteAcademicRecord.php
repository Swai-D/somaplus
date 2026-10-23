<?php

namespace App\Domain\Academics;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TimetableEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * Removes a record of the academic structure, unless something still hangs off
 * it. Features that later refer to these records (students, marks, fees, timetable) keep
 * restrictive foreign keys and add their own check here, because a school's
 * history is never overwritten or removed.
 */
final class DeleteAcademicRecord
{
    public function handle(Model $record): void
    {
        $blocked = match (true) {
            $record instanceof AcademicYear => match (true) {
                $record->terms()->exists() => 'academics.years.has_terms',
                Enrollment::query()->where('academic_year_id', $record->id)->exists() => 'academics.years.has_students',
                Exam::query()->where('academic_year_id', $record->id)->exists() => 'academics.years.has_exams',
                default => null,
            },
            $record instanceof SchoolClass => match (true) {
                $record->streams()->exists() => 'academics.classes.has_streams',
                Enrollment::query()->where('school_class_id', $record->id)->exists() => 'academics.classes.has_students',
                FeeStructure::query()->where('school_class_id', $record->id)->exists() => 'academics.classes.has_fees',
                TimetableEntry::query()->where('school_class_id', $record->id)->exists() => 'academics.classes.has_timetable',
                default => null,
            },
            $record instanceof Stream => match (true) {
                Enrollment::query()->where('stream_id', $record->id)->exists() => 'academics.streams.has_students',
                TimetableEntry::query()->where('stream_id', $record->id)->exists() => 'academics.streams.has_timetable',
                default => null,
            },
            $record instanceof Term => match (true) {
                Exam::query()->where('term_id', $record->id)->exists() => 'academics.terms.has_exams',
                FeeStructure::query()->where('term_id', $record->id)->exists() => 'academics.terms.has_fees',
                TimetableEntry::query()->where('term_id', $record->id)->exists() => 'academics.terms.has_timetable',
                default => null,
            },
            $record instanceof Subject => match (true) {
                ExamMark::query()->where('subject_id', $record->id)->exists() => 'academics.subjects.has_marks',
                TimetableEntry::query()->where('subject_id', $record->id)->exists() => 'academics.subjects.has_timetable',
                default => null,
            },
            default => null,
        };

        if ($blocked !== null) {
            throw ValidationException::withMessages(['delete' => __($blocked)]);
        }

        $record->delete();
    }
}
