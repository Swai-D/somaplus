<?php

namespace App\Domain\Settings;

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;

/**
 * The facts shown on the dashboard. Only real data: a school sees its own
 * name, plan and subscription state; the platform sees how many schools exist.
 */
final class BuildDashboardSummary
{
    /**
     * @return array<string, mixed>
     */
    public function handle(User $user): array
    {
        $school = app(TenantContext::class)->current();

        if ($school === null) {
            return [
                'scope' => 'platform',
                'schools' => Tenant::query()->count(),
            ];
        }

        return [
            'scope' => 'school',
            'school' => $school->name,
            'plan' => $school->plan?->name,
            'status' => $school->subscription_status->value,
            'grace_ends_at' => $school->grace_ends_at,
            'roles' => $user->getRoleNames()->map(fn ($role) => __('roles.'.$role))->values()->all(),
        ];
    }
}
