<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use App\Tenancy\TenancyViolation;
use App\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Marks an Eloquent model as owned by one school (tenant).
 *
 * The table needs a `tenant_id` ULID column. Every query is scoped to the
 * current tenant, new rows get the current tenant's id, and a row can never
 * be written for, or moved to, another tenant.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model): void {
            $context = app(TenantContext::class);
            $currentId = $context->currentId();
            $tenantId = $model->getAttribute('tenant_id');

            if ($tenantId === null) {
                if ($currentId === null) {
                    throw TenancyViolation::noTenant($model::class);
                }

                $model->setAttribute('tenant_id', $currentId);

                return;
            }

            if ($currentId !== null && $tenantId !== $currentId && ! $context->isSystem()) {
                throw TenancyViolation::otherTenant($model::class);
            }
        });

        static::updating(function (Model $model): void {
            if ($model->isDirty('tenant_id')) {
                throw TenancyViolation::tenantChanged($model::class);
            }
        });
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
