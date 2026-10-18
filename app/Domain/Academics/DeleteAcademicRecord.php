<?php

namespace App\Domain\Academics;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
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
            $record instanceof AcademicYear => $record->terms()->exists() ? 'academics.years.has_terms' : null,
            $record instanceof SchoolClass => $record->streams()->exists() ? 'academics.classes.has_streams' : null,
            default => null,
        };

        if ($blocked !== null) {
            throw ValidationException::withMessages(['delete' => __($blocked)]);
        }

        $record->delete();
    }
}
