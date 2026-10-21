<?php

namespace App\Policies;

use App\Models\User;

/** Only people who may manage the school's settings (the owner) look at or change how admission numbers are made. */
class AdmissionSettingPolicy
{
    public function manage(User $user): bool
    {
        return $user->can('settings.manage');
    }
}
