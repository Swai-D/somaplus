<?php

namespace App\Policies;

use App\Models\TimetableEntry;
use App\Models\User;

/**
 * Who may look at and change the lessons of the timetable. Permissions are checked inside the
 * school's context, so a role never counts at another school.
 */
class TimetableEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('timetable.view');
    }

    public function view(User $user, TimetableEntry $entry): bool
    {
        return $user->can('timetable.view');
    }

    public function create(User $user): bool
    {
        return $user->can('timetable.manage');
    }

    public function update(User $user, TimetableEntry $entry): bool
    {
        return $user->can('timetable.manage');
    }

    public function delete(User $user, TimetableEntry $entry): bool
    {
        return $user->can('timetable.manage');
    }
}
