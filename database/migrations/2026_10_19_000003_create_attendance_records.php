<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per student and day. The class, stream and year are the placement
        // the student had on that day, so later moves never change old registers.
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->restrictOnDelete();
            $table->date('attended_on');
            $table->string('status', 10);
            $table->string('note', 255)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'attended_on']);
            $table->index(['tenant_id', 'school_class_id', 'attended_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
