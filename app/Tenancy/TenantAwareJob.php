<?php

namespace App\Tenancy;

/**
 * Add to a queued job so it runs for the tenant that dispatched it:
 *
 *     MyJob::dispatch(...)  // inside the job's constructor, call $this->forCurrentTenant()
 */
trait TenantAwareJob
{
    public ?string $tenantId = null;

    public function forCurrentTenant(): static
    {
        $tenantId = TenantContext::instance()->currentId();

        if ($tenantId === null) {
            throw TenancyViolation::noTenant(static::class);
        }

        $this->tenantId = $tenantId;

        return $this;
    }

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new RestoreTenantContext];
    }
}
