<?php

use App\Domain\Settings\SyncTenantRoles;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

/**
 * config/roles.php only seeds new schools, so existing schools receive reminders.send here.
 * Syncing only adds; it never revokes anything.
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
