<?php

namespace App\Policies;

use App\Models\TimetablePeriod;
use App\Models\User;

/** Who may set the lesson slots of the school's day: only those who manage the timetable. */
class TimetablePeriodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('timetable.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('timetable.manage');
    }

    public function update(User $user, TimetablePeriod $period): bool
    {
        return $user->can('timetable.manage');
    }

    public function delete(User $user, TimetablePeriod $period): bool
    {
        return $user->can('timetable.manage');
    }
}
