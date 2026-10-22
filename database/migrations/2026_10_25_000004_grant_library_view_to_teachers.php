<?php

use App\Domain\Settings\SyncTenantRoles;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

/**
 * config/roles.php only seeds new schools, and syncing only adds, never revokes, so
 * the teachers of the existing schools receive `library.view` here (view only: putting
 * books out stays for the owner and the head teacher).
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
