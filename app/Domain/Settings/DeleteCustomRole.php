<?php

namespace App\Domain\Settings;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/** Deletes a custom role that nobody holds; a role in use must first be taken off its people. */
final class DeleteCustomRole
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Role $role): void
    {
        if (RoleCatalog::holders()[$role->id] ?? 0) {
            throw ValidationException::withMessages(['delete' => __('custom_roles.in_use')]);
        }

        DB::transaction(function () use ($role): void {
            $this->audit->handle('role.deleted', $role);
            $role->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
