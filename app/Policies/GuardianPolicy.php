<?php

namespace App\Policies;

use App\Models\Guardian;
use App\Models\User;

/**
 * Who may look at the guardians of a school and who may change them. Looking at a guardian is
 * looking at the family of a pupil, so it needs the same permission as the pupils; changing one
 * needs the permission that changes a pupil.
 */
class GuardianPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('students.view');
    }

    public function view(User $user, Guardian $guardian): bool
    {
        return $user->can('students.view');
    }

    public function create(User $user): bool
    {
        return $user->can('students.manage');
    }

    public function update(User $user, Guardian $guardian): bool
    {
        return $user->can('students.manage');
    }

    /** Giving or taking away a person's account is managing users: owner only. */
    public function managePortal(User $user, Guardian $guardian): bool
    {
        return $user->can('settings.manage');
    }

    public function delete(User $user, Guardian $guardian): bool
    {
        return $user->can('students.manage');
    }
}
