<?php

use App\Domain\Settings\SyncTenantRoles;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

/**
 * config/roles.php only seeds new schools, so the existing ones receive the
 * permissions of the library section here. Syncing only adds; it never revokes.
 */
return new class extends Migration
{
    public function up(): void
    {
        $sync = app(SyncTenantRoles::class);

        Tenant::query()->each(fn (Tenant $tenant) => $sync->handle($tenant));
    }

    public function down(): void
    {
        // Permissions are never revoked by a rollback.
    }
};
