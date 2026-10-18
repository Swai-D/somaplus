<?php

namespace App\Domain\Settings;

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Makes sure a school has every role in config/roles.php, and that each role
 * has the permissions config/roles.php gives it.
 *
 * It is idempotent and only ever adds: roles and permissions that already exist
 * are left alone and nothing is revoked.
 */
class SyncTenantRoles
{
    private const GUARD = 'web';

    public function handle(Tenant $tenant): void
    {
        TenantContext::run($tenant, function (): void {
            $roles = [];

            foreach (config('roles.roles', []) as $name) {
                $roles[$name] = Role::findOrCreate($name, self::GUARD);
            }

            foreach (config('roles.permissions', []) as $permission => $roleNames) {
                Permission::findOrCreate($permission, self::GUARD);

                foreach ($roleNames as $roleName) {
                    $role = $roles[$roleName]
                        ?? throw new InvalidArgumentException("Permission [{$permission}] refers to unknown role [{$roleName}].");

                    $role->givePermissionTo($permission);
                }
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
