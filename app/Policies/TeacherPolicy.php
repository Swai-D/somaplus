<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

/**
 * Who may see and change the teachers of a school. Reading needs
 * `teachers.view` and changing needs `teachers.manage`; both are checked
 * inside the school's context.
 */
class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('teachers.view');
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $user->can('teachers.view');
    }

    public function create(User $user): bool
    {
        return $user->can('teachers.manage');
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $user->can('teachers.manage');
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->can('teachers.manage');
    }
}
