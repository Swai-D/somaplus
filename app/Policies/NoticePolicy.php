<?php

namespace App\Policies;

use App\Models\Notice;
use App\Models\User;

/**
 * Who may see and change the notice board. Reading needs `communication.view`
 * and changing needs `communication.manage`, like the rest of the section.
 */
class NoticePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('communication.view');
    }

    public function view(User $user, Notice $notice): bool
    {
        return $user->can('communication.view');
    }

    public function create(User $user): bool
    {
        return $user->can('communication.manage');
    }

    public function update(User $user, Notice $notice): bool
    {
        return $user->can('communication.manage');
    }

    public function delete(User $user, Notice $notice): bool
    {
        return $user->can('communication.manage');
    }
}
