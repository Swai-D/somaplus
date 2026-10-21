<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A mobile-money prompt sent to a payer's phone for one invoice. The phone number is not kept (it is only
        // needed to send the prompt) and neither is the provider's callback body.
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('idempotency_key', 100);
            $table->string('reference', 40);
            $table->string('driver', 30);
            $table->string('provider', 20);
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3);
            $table->string('status', 20);
            $table->string('failure_reason', 250)->nullable();
            $table->string('provider_reference', 100)->nullable();
            $table->string('mno_reference', 100)->nullable();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->restrictOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'idempotency_key']);
            $table->unique(['tenant_id', 'reference']);
            $table->index(['tenant_id', 'invoice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
