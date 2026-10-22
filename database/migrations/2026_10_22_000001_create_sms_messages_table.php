<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A fee reminder text to one guardian about one invoice. Neither the phone number nor the text is kept:
        // both are worked out again when the message is sent (the guardian's number may change, the balance too).
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->foreignId('guardian_id')->constrained('guardians')->restrictOnDelete();
            $table->foreignId('sent_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('kind', 20);
            $table->string('locale', 5);
            $table->date('day');
            $table->string('status', 20);
            $table->string('provider_reference', 100)->nullable();
            $table->string('error', 250)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // One reminder per invoice and guardian per day, however many times the button is pressed.
            $table->unique(['tenant_id', 'kind', 'invoice_id', 'guardian_id', 'day']);
            $table->index(['tenant_id', 'invoice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
