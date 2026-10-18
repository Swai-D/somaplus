<?php

namespace App\Tenancy;

use App\Models\Tenant;
use Closure;

/**
 * Job middleware: runs a queued job inside the tenant it was dispatched for.
 *
 * The job carries only the tenant id in its serialized payload; the tenant is
 * loaded again when the job runs. Use it through the TenantAwareJob trait.
 */
final class RestoreTenantContext
{
    public function handle(object $job, Closure $next): mixed
    {
        $tenantId = $job->tenantId ?? null;

        if (! is_string($tenantId) || $tenantId === '') {
            throw TenancyViolation::jobWithoutTenant($job::class);
        }

        $tenant = Tenant::query()->find($tenantId);

        if ($tenant === null) {
            throw TenancyViolation::unknownTenant($tenantId);
        }

        return TenantContext::run($tenant, fn () => $next($job));
    }
}
