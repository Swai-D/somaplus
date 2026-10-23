<?php

namespace App\Domain\Timetable;

use App\Models\TimetableEntry;

/** Takes one lesson off the timetable. Nothing else refers to a lesson, so it can always go. */
final class DeleteTimetableEntry
{
    public function handle(TimetableEntry $entry): void
    {
        $entry->delete();
    }
}
