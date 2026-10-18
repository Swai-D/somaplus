<?php

namespace App\Models\Scopes;

use App\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Scopes users: inside a school only that school's users are visible; on the
 * central domain (no tenant) only super-admins, who have no school, are.
 * Everything is visible only inside TenantContext::runAsSystem().
 */
final class UserScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $context = app(TenantContext::class);

        if ($context->isSystem()) {
            return;
        }

        $column = $model->qualifyColumn('tenant_id');
        $tenantId = $context->currentId();

        if ($tenantId === null) {
            $builder->whereNull($column);

            return;
        }

        $builder->where($column, $tenantId);
    }
}
