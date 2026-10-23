<?php

namespace App\Domain\Timetable;

use App\Models\TimetablePeriod;
use Illuminate\Validation\ValidationException;

/** Removes a slot of the day, unless a lesson still sits in it. */
final class DeleteTimetablePeriod
{
    public function handle(TimetablePeriod $period): void
    {
        if ($period->entries()->exists()) {
            throw ValidationException::withMessages(['delete' => __('timetable.errors.period_in_use')]);
        }

        $period->delete();
    }
}
