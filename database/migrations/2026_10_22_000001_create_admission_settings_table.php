<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per school. A school without a row keeps typing admission numbers by hand (the old behaviour).
        Schema::create('admission_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('mode', 10)->default('manual');
            $table->string('prefix', 10)->default('');
            $table->string('separator', 1)->default('-');
            $table->string('year_format', 4)->default('YYYY');
            $table->unsignedTinyInteger('digits')->default(4);
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_settings');
    }
};
