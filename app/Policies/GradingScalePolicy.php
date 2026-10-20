<?php

namespace App\Policies;

use App\Models\GradingScale;
use App\Models\User;

/**
 * Anyone who may see exams may see the scales; only those with `grades.manage` change them.
 */
class GradingScalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('exams.view');
    }

    public function view(User $user, GradingScale $scale): bool
    {
        return $user->can('exams.view');
    }

    public function create(User $user): bool
    {
        return $user->can('grades.manage');
    }

    public function update(User $user, GradingScale $scale): bool
    {
        return $user->can('grades.manage');
    }

    public function delete(User $user, GradingScale $scale): bool
    {
        return $user->can('grades.manage');
    }
}
