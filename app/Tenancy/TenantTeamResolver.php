<?php

namespace App\Tenancy;

use Illuminate\Database\Eloquent\Model;
use LogicException;
use Spatie\Permission\Contracts\PermissionsTeamResolver;

/**
 * Tells spatie/laravel-permission which school's roles to use: always the
 * current tenant. It cannot be set from elsewhere, so a role check can never
 * be answered for a school other than the one the request or job is working for.
 */
final class TenantTeamResolver implements PermissionsTeamResolver
{
    public function getPermissionsTeamId(): ?string
    {
        return TenantContext::instance()->currentId();
    }

    public function setPermissionsTeamId(int|string|Model|null $id): void
    {
        $id = $id instanceof Model ? $id->getKey() : $id;

        if ($id === $this->getPermissionsTeamId()) {
            return;
        }

        throw new LogicException(
            'The permissions team is the current tenant and cannot be set directly; use TenantContext::run().',
        );
    }
}
