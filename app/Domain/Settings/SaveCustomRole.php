<?php

namespace App\Domain\Settings;

use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Makes or changes a custom role of the current school. The five default roles are never touched here.
 * The permissions are limited to the ones a custom role may have, plus the ones they need to work,
 * and the change is written to the audit log (role id only).
 */
final class SaveCustomRole
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{name: string, permissions: array<int, string>}  $data
     */
    public function handle(?Role $role, array $data): Role
    {
        $role = DB::transaction(function () use ($role, $data): Role {
            $created = $role === null;
            $name = trim($data['name']);

            if ($role === null) {
                $role = Role::query()->create(['name' => $name, 'guard_name' => 'web', 'tenant_id' => TenantContext::instance()->currentId()]);
            } elseif ($role->name !== $name) {
                $role->name = $name;
                $role->save();
            }

            $permissions = RoleCatalog::withImplied($data['permissions']);
            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission, 'web');
            }
            $role->syncPermissions($permissions);

            $this->audit->handle($created ? 'role.created' : 'role.updated', $role);

            return $role;
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role;
    }
}
