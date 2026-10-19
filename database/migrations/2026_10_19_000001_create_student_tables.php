<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('admission_number', 30);
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('gender', 10);
            $table->date('date_of_birth');
            $table->date('admitted_on');
            $table->string('status', 10)->default('active');
            $table->date('left_on')->nullable();
            $table->string('left_reason', 255)->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'admission_number']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('name', 150);
            $table->string('phone', 30);
            $table->string('email', 150)->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'phone']);
        });

        Schema::create('guardian_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('guardian_id')->constrained('guardians')->restrictOnDelete();
            $table->string('relationship', 20);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'guardian_id']);
        });

        // One row per placement. A promotion or a move adds a row; nothing is overwritten,
        // and the placement that counts for a year is the newest row of that student and year.
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->restrictOnDelete();
            $table->date('enrolled_on');
            $table->timestamps();

            $table->index(['student_id', 'academic_year_id']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 60);
            $table->string('subject_type', 60);
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('guardian_student');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('students');
    }
};
