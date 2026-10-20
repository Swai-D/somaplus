<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A grading scale is a list of bands; a band is the lowest percentage that earns a grade.
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('grading_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('grading_scale_id')->constrained('grading_scales')->restrictOnDelete();
            $table->decimal('min_mark', 5, 2);
            $table->string('grade', 5);
            $table->unsignedTinyInteger('points')->nullable();
            $table->string('remark', 100)->nullable();
            $table->timestamps();

            $table->unique(['grading_scale_id', 'min_mark']);
            $table->unique(['grading_scale_id', 'grade']);
        });

        // An exam keeps the scale it was created with. A scale that an exam uses is never changed,
        // so the grades of a past term can always be worked out again the same way.
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('term_id')->constrained('terms')->restrictOnDelete();
            $table->foreignId('grading_scale_id')->constrained('grading_scales')->restrictOnDelete();
            $table->string('name', 100);
            $table->unsignedSmallInteger('max_mark');
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'term_id', 'name']);
        });

        // Only the score is stored. The grade is worked out from the exam's scale whenever it is shown.
        // The class and stream are the student's placement when the mark was entered.
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->restrictOnDelete();
            $table->decimal('score', 6, 2)->nullable();
            $table->boolean('is_absent')->default(false);
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['exam_id', 'subject_id', 'student_id']);
            $table->index(['exam_id', 'school_class_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('grading_bands');
        Schema::dropIfExists('grading_scales');
    }
};
