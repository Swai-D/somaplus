<?php

namespace App\Domain\Settings;

use App\Models\AcademicYear;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;

/**
 * The facts shown on the dashboard. Only real data: a school sees its own
 * name, plan, subscription state, current academic year and term, and the figures its person may see
 * (`SchoolDashboardFigures`); the platform sees how many schools exist.
 */
final class BuildDashboardSummary
{
    public function __construct(private readonly SchoolDashboardFigures $figures) {}

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

        $year = AcademicYear::query()->where('is_current', true)->first();
        $term = $year?->terms()
            ->whereDate('starts_on', '<=', today())
            ->whereDate('ends_on', '>=', today())
            ->first();

        return [
            'scope' => 'school',
            'academic_year' => $year?->name,
            'term' => $term?->name,
            'school' => $school->name,
            'plan' => $school->plan?->name,
            'status' => $school->subscription_status->value,
            'grace_ends_at' => $school->grace_ends_at,
            'roles' => $user->getRoleNames()->map(fn ($role) => RoleCatalog::label($role))->values()->all(),
            'figures' => $this->figures->handle($user, $school, $year),
        ];
    }
}
