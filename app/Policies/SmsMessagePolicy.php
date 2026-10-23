<?php

namespace App\Policies;

use App\Models\User;

/** Fee reminders reach guardians, so only those with `reminders.send` (owner, accountant) may send or look. */
class SmsMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('reminders.send');
    }

    public function send(User $user): bool
    {
        return $user->can('reminders.send');
    }
}
