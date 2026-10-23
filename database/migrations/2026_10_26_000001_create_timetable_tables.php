<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The lesson slots of the school's day (for example "Period 1, 08:00 to 08:40" or "Break").
        // A school keeps one list; every class uses it.
        Schema::create('timetable_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('name', 50);
            $table->time('starts_at');
            $table->time('ends_at');
            $table->boolean('is_break')->default(false);
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
            $table->index(['tenant_id', 'starts_at']);
        });

        // One lesson: a subject for a class (and maybe one stream) in one period of one weekday of a term.
        // Weekday is ISO: 1 = Monday ... 7 = Sunday. The teacher is optional and is only unset (never the
        // lesson removed) when the teacher leaves the school. Clashes are refused by SaveTimetableEntry
        // inside a transaction that locks the period, because a unique index cannot see a nullable stream.
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('term_id')->constrained('terms')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('timetable_period_id')->constrained('timetable_periods')->restrictOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->string('room', 50)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'term_id', 'weekday', 'timetable_period_id'], 'timetable_slot_index');
            $table->index(['term_id', 'school_class_id']);
            $table->index(['term_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
        Schema::dropIfExists('timetable_periods');
    }
};
