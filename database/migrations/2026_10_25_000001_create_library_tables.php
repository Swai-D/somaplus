<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A book of the library. The price is an integer of minor units, as everywhere else in
        // the school; `quantity` is the copies the school owns, and what is available is counted
        // from the books that are still out, never typed by hand.
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->restrictOnDelete();
            $table->string('name', 200);
            $table->string('publisher', 150)->nullable();
            $table->string('author', 150)->nullable();
            $table->string('book_number', 50)->nullable();
            $table->string('rack_number', 30)->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->unsignedInteger('price')->nullable();
            $table->timestamps();

            // The number of the book is the school's own, so it is unique inside it.
            $table->unique(['tenant_id', 'book_number']);
        });

        // A member of the library: one pupil of the school who can borrow, with the card
        // number the school gives them. Phone is shown from the pupil's guardian.
        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('student_id')->unique()->constrained('students')->restrictOnDelete();
            $table->string('card_number', 40);
            $table->date('joined_on');
            $table->timestamps();

            $table->unique(['tenant_id', 'card_number']);
        });

        // One book that went out, and the day it came back. The copy is still out while
        // `returned_on` is empty, and that is what keeps the book's available count down.
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('book_id')->constrained('books')->restrictOnDelete();
            $table->foreignId('member_id')->constrained('library_members')->restrictOnDelete();
            $table->date('issued_on');
            $table->date('returned_on')->nullable();
            $table->timestamps();

            $table->index(['returned_on', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_issues');
        Schema::dropIfExists('library_members');
        Schema::dropIfExists('books');
    }
};
