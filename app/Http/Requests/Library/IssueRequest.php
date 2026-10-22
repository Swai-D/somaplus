<?php

namespace App\Http\Requests\Library;

use App\Models\Book;
use App\Models\BookIssue;
use Closure;
use Illuminate\Validation\Rule;

/**
 * A book put out to one member: which book, to whom and the day it went out.
 * The copy counts against the book's available copies, and a day cannot come back.
 */
class IssueRequest extends LibraryRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', BookIssue::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'member_id' => ['required', $this->inSchool('library_members')],
            'book_id' => [
                'required',
                $this->inSchool('books'),
                // The rule of a copy: the book must be on the shelf today.
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($this->available() <= 0) {
                        $fail(__('library.issues.none_available'));
                    }
                },
            ],
            'issued_on' => ['required', 'date', 'before_or_equal:today'],
            // The day the school expects the copy back; never before the day it went out.
            'due_on' => ['required', 'date', 'after_or_equal:issued_on'],
        ];
    }

    /** The copies of the chosen book that are on the shelf, zero when the choice is not valid. */
    public function available(): int
    {
        $book = Book::query()->find((int) $this->input('book_id'));
        if ($book === null) {
            return 0;
        }

        return $book->availableCount();
    }
}
