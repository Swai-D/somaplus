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

    /** A student's report card holds marks, which are personal data: it needs `results.view`. */
    public function viewReport(User $user, Student $student): bool
    {
        return $user->can('results.view');
    }

    /** An ID card shows a child's name and number: only people who manage students print them. */
    public function printIdCard(User $user): bool
    {
        return $user->can('students.manage');
    }
}
