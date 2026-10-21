<?php

namespace App\Policies;

use App\Models\User;

/**
 * Integrations hold a school's provider credentials: only those with `settings.manage` (owners) may see or change them.
 */
class TenantIntegrationPolicy
{
    public function manage(User $user): bool
    {
        return $user->can('settings.manage');
    }
}
