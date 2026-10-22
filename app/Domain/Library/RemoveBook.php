<?php

namespace App\Domain\Library;

use App\Domain\Settings\RecordAudit;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Takes a book out of the library for good. A book the school once lent to a member
 * cannot simply vanish, because the record of the issue says it is this book, so the
 * school refunds or removes the records first. The same number of a book that went
 * out keeps the quantity honest.
 */
final class RemoveBook
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Book $book): void
    {
        if ($book->issues()->exists()) {
            throw ValidationException::withMessages([
                'delete' => __('library.books.still_has_issues'),
            ]);
        }

        DB::transaction(function () use ($book): void {
            $this->audit->handle('book.deleted', $book);

            $book->delete();
        });
    }
}
