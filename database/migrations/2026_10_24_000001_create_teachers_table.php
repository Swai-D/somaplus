<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A teacher is a person of the school who signs in (a user with the teacher role)
        // and what the school knows about them: the staff number, the phone they can be
        // reached on, the subjects they teach and the classes they are in.
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('staff_number', 30)->nullable();
            $table->string('phone', 30);
            $table->string('gender', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('qualification', 150)->nullable();
            $table->string('contract_type', 30)->nullable();
            $table->string('shift', 30)->nullable();
            $table->date('joined_on');
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // The staff number of a teacher is the school's own, so it is unique inside it.
            $table->unique(['tenant_id', 'staff_number']);
        });

        // The subjects a teacher teaches.
        Schema::create('teacher_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();

            $table->unique(['teacher_id', 'subject_id']);
        });

        // The classes a teacher is in.
        Schema::create('teacher_class', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();

            $table->unique(['teacher_id', 'school_class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_class');
        Schema::dropIfExists('teacher_subject');
        Schema::dropIfExists('teachers');
    }
};
