<?php

namespace App\Policies;

use App\Models\User;

/**
 * Who may take and look at attendance. Permissions are checked inside the
 * school's context, so a role never counts at another school.
 */
class AttendanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('attendance.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('attendance.manage');
    }
}
