<?php

namespace App\Support;

use App\Tenancy\TenantContext;

final class Plans
{
    /**
     * The plan of the current tenant, usable outside requests too
     * (jobs, commands) wherever a TenantContext is active.
     */
    public static function current(): PlanAccess
    {
        $tenant = TenantContext::instance()->current();

        return new PlanAccess($tenant?->plan);
    }
}
