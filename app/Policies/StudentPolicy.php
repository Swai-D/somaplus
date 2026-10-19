<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

/**
 * Who may see and change students, guardians and placements. Permissions are
 * checked inside the school's context, so a role never counts at another school.
 */
class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('students.view');
    }

    public function view(User $user, Student $student): bool
    {
        return $user->can('students.view');
    }

    public function create(User $user): bool
    {
        return $user->can('students.manage');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->can('students.manage');
    }
}
