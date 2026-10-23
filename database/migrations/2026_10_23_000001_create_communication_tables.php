<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A notice on the school's notice board. The day is when the notice
        // speaks from, not when it was typed.
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->date('day');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'day']);
        });

        // An entry of the school's calendar, for example a parents' meeting.
        // The end may be left open, for an all-day event.
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('notices');
    }
};
