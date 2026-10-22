<?php

namespace App\Domain\Library;

use App\Domain\Settings\RecordAudit;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\LibraryMember;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Puts one copy of a book out to one member of the library. The copy comes off the
 * shelf the call is made on, so two people cannot borrow the last copy at once.
 */
final class IssueBook
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Book $book, LibraryMember $member, string $issuedOn, string $dueOn): BookIssue
    {
        return DB::transaction(function () use ($book, $member, $issuedOn, $dueOn): BookIssue {
            $out = BookIssue::query()
                ->where('book_id', $book->id)
                ->whereNull('returned_on')
                ->lockForUpdate()
                ->count();

            if ($out >= $book->quantity) {
                throw ValidationException::withMessages([
                    'book_id' => __('library.issues.none_available'),
                ]);
            }

            $issue = new BookIssue;
            $issue->forceFill([
                'tenant_id' => TenantContext::instance()->currentId(),
                'book_id' => $book->id,
                'member_id' => $member->id,
                'issued_on' => $issuedOn,
                'due_on' => $dueOn,
                'returned_on' => null,
            ]);
            $issue->save();

            $this->audit->handle('issue.created', $issue);

            return $issue;
        });
    }
}
