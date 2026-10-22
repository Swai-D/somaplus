<?php

namespace App\Policies;

use App\Models\User;

/** Only people who may manage the school's settings (the owner) change the school's contact details. */
class SchoolProfilePolicy
{
    public function manage(User $user): bool
    {
        return $user->can('settings.manage');
    }
}
