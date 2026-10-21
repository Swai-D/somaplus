<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per school and channel: the provider (driver) the school chose, its plain settings and its
        // secrets (encrypted by the model; never shown again after saving). Off until the school switches it on.
        Schema::create('tenant_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('channel', 30);
            $table->string('driver', 50);
            $table->boolean('enabled')->default(false);
            $table->json('settings')->nullable();
            $table->text('secrets')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'channel']);
        });

        // Every verified payment notice a provider sends, kept for reconciliation: what was applied,
        // what matched no invoice and what was refused. No raw body and no personal data is stored.
        Schema::create('payment_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('provider_reference', 100);
            $table->string('control_number', 30)->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('status', 20);
            $table->string('reason', 100)->nullable();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->restrictOnDelete();
            $table->timestamp('received_at');
            $table->timestamps();

            $table->unique(['tenant_id', 'provider_reference']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_notifications');
        Schema::dropIfExists('tenant_integrations');
    }
};
