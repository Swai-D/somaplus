<?php

use App\Domain\Settings\SyncTenantRoles;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The account a guardian signs in with, when the school has given them access to the family pages.
        Schema::table('guardians', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
        });

        // Parents get the new permission in every school that exists already.
        TenantContext::runAsSystem(function (): void {
            Tenant::query()->each(fn (Tenant $tenant) => app(SyncTenantRoles::class)->handle($tenant));
        });
    }

    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
