<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The messages table also carries the return reminders of the library: a message is
 * then about one `book_issue`, and `invoice_id` may be empty. The one-per-day rules
 * are rebuilt as partial unique indexes, because a unique key ignores NULLs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_messages', function (Blueprint $table): void {
            $table->foreignId('invoice_id')->nullable()->change();
            $table->foreignId('book_issue_id')->nullable()->after('invoice_id');
        });

        Schema::table('sms_messages', function (Blueprint $table): void {
            $table->foreign('book_issue_id')->constrained('book_issues')->restrictOnDelete();
        });

        Schema::table('sms_messages', function (Blueprint $table): void {
            $table->dropUnique('sms_messages_tenant_id_kind_invoice_id_guardian_id_day_unique');
        });

        // A reminder about one invoice, and one about one copy, never twice a day.
        DB::statement('create unique index sms_messages_fee_unique on sms_messages (tenant_id, kind, invoice_id, guardian_id, day) where invoice_id is not null');
        DB::statement('create unique index sms_messages_return_unique on sms_messages (tenant_id, kind, book_issue_id, guardian_id, day) where book_issue_id is not null');
    }

    public function down(): void
    {
        Schema::table('sms_messages', function (Blueprint $table): void {
            $table->dropIndex('sms_messages_fee_unique');
            $table->dropIndex('sms_messages_return_unique');
            $table->unique(['tenant_id', 'kind', 'invoice_id', 'guardian_id', 'day'], 'sms_messages_tenant_id_kind_invoice_id_guardian_id_day_unique');
        });

        Schema::table('sms_messages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('book_issue_id');
        });

        Schema::table('sms_messages', function (Blueprint $table): void {
            $table->foreignId('invoice_id')->nullable(false)->change();
        });
    }
};
