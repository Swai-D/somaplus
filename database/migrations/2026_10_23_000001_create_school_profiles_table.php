<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per school: the contact details printed at the top of receipts and report cards.
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('email', 190)->nullable();
            $table->string('address', 250)->nullable();
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};
