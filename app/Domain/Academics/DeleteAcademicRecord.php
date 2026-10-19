<?php

namespace App\Domain\Academics;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Stream;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * Removes a record of the academic structure, unless something still hangs off
 * it. Features that later refer to these records (students, marks, fees) keep
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
                default => null,
            },
            $record instanceof SchoolClass => match (true) {
                $record->streams()->exists() => 'academics.classes.has_streams',
                Enrollment::query()->where('school_class_id', $record->id)->exists() => 'academics.classes.has_students',
                default => null,
            },
            $record instanceof Stream => Enrollment::query()->where('stream_id', $record->id)->exists() ? 'academics.streams.has_students' : null,
            default => null,
        };

        if ($blocked !== null) {
            throw ValidationException::withMessages(['delete' => __($blocked)]);
        }

        $record->delete();
    }
}
