<?php

namespace App\Domain\Timetable;

use App\Models\Term;

/**
 * The term a timetable page opens on when the person has not chosen one: the term that
 * contains today, otherwise the newest term of the current academic year, otherwise the
 * newest term of the school.
 */
final class TimetableTerm
{
    public function current(): ?Term
    {
        $today = now()->toDateString();

        return Term::query()->whereDate('starts_on', '<=', $today)->whereDate('ends_on', '>=', $today)->first()
            ?? Term::query()->whereHas('academicYear', fn ($year) => $year->where('is_current', true))->orderByDesc('starts_on')->first()
            ?? Term::query()->orderByDesc('starts_on')->first();
    }
}
