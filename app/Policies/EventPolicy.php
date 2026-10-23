<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

/**
 * Who may see and change the school's calendar. Reading needs `communication.view`
 * and changing needs `communication.manage`, like the rest of the section.
 */
class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('communication.view');
    }

    public function view(User $user, Event $event): bool
    {
        return $user->can('communication.view');
    }

    public function create(User $user): bool
    {
        return $user->can('communication.manage');
    }

    public function update(User $user, Event $event): bool
    {
        return $user->can('communication.manage');
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->can('communication.manage');
    }
}
