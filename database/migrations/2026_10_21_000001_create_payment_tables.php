<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A payment is never edited and never deleted. A mistake is undone by a reversal (a separate record),
        // and an invoice's balance is always worked out from its payments, never stored.
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3);
            $table->string('method', 20);
            // "manual" (typed in by a person) or "integration" (received from a bank or other provider).
            $table->string('source', 20)->default('manual');
            $table->string('reference', 100)->nullable();
            $table->string('control_number', 20)->nullable();
            // Sent again by a client or provider, the same key gives back the same payment instead of a second one.
            $table->string('idempotency_key', 100);
            $table->string('provider_reference', 100)->nullable();
            $table->date('paid_on');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'idempotency_key']);
            $table->unique(['tenant_id', 'provider_reference']);
            $table->index(['tenant_id', 'invoice_id']);
        });

        // One receipt per payment, numbered without gaps within the school.
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->unsignedBigInteger('number');
            $table->foreignId('payment_id')->constrained('payments')->restrictOnDelete();
            $table->date('issued_on');
            $table->timestamps();

            $table->unique(['tenant_id', 'number']);
            $table->unique('payment_id');
        });

        Schema::create('payment_reversals', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->restrictOnDelete();
            $table->string('reason', 250);
            $table->foreignId('reversed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->date('reversed_on');
            $table->timestamps();

            $table->unique('payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_reversals');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('payments');
    }
};
