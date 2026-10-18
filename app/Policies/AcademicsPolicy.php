<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Who may see and change the academic structure of a school. Permissions are
 * checked inside the school's context, so a role never counts at another school.
 */
abstract class AcademicsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('academics.view');
    }

    public function view(User $user, Model $record): bool
    {
        return $user->can('academics.view');
    }

    public function create(User $user): bool
    {
        return $user->can('academics.manage');
    }

    public function update(User $user, Model $record): bool
    {
        return $user->can('academics.manage');
    }

    public function delete(User $user, Model $record): bool
    {
        return $user->can('academics.manage');
    }
}
