<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** The day a copy that is out has to come back, chosen by the school when it issues the book. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_issues', function (Blueprint $table): void {
            $table->date('due_on')->nullable()->after('issued_on');
        });
    }

    public function down(): void
    {
        Schema::table('book_issues', function (Blueprint $table): void {
            $table->dropColumn('due_on');
        });
    }
};
