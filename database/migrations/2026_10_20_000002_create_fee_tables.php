<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The currency every amount of a school is held in (minor units). Changing it is a later settings feature.
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('currency', 3)->default('TZS');
        });

        // Numbers without gaps: the next number is taken under a row lock inside the transaction that uses it.
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('type', 30);
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'type']);
        });

        // A fee structure is the list of fees for one class in one term.
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('term_id')->constrained('terms')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['tenant_id', 'term_id', 'school_class_id', 'name']);
        });

        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->restrictOnDelete();
            $table->string('name', 100);
            $table->unsignedBigInteger('amount');
            $table->timestamps();

            $table->unique(['fee_structure_id', 'name']);
        });

        // An invoice is a snapshot: its lines and total never change when a structure is edited later.
        // Balances are derived from payments (a later feature); nothing about payment is stored here.
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->unsignedBigInteger('number');
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('term_id')->constrained('terms')->restrictOnDelete();
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->restrictOnDelete();
            $table->string('currency', 3);
            $table->unsignedBigInteger('total');
            $table->string('control_number', 20)->nullable();
            $table->date('issued_on');
            $table->timestamps();

            $table->unique(['tenant_id', 'number']);
            // Generating twice never makes a second invoice of the same structure for a student.
            $table->unique(['tenant_id', 'student_id', 'fee_structure_id']);
            $table->unique(['tenant_id', 'control_number']);
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->string('name', 100);
            $table->unsignedBigInteger('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('fee_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('document_sequences');
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
