<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Null for super-admins, who belong to the platform and not to a school.
            $table->foreignUlid('tenant_id')->nullable()->constrained('tenants')->restrictOnDelete();
            $table->boolean('is_super_admin')->default(false);

            // The same address may belong to a person at two different schools.
            $table->dropUnique(['email']);
            $table->unique(['tenant_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'email']);
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn('is_super_admin');
            $table->unique(['email']);
        });
    }
};
