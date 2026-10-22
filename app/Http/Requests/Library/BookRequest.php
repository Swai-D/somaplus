<?php

namespace App\Http\Requests\Library;

use App\Models\Book;
use App\Support\Money;

/**
 * A book of the library: what the school holds, where it is and what it costs.
 */
class BookRequest extends LibraryRequest
{
    public function authorize(): bool
    {
        $book = $this->route('book');

        return $book instanceof Book
            ? (bool) $this->user()?->can('update', $book)
            : (bool) $this->user()?->can('create', Book::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $book = $this->route('book');
        $ignore = $book instanceof Book ? $book->id : null;

        return [
            'subject_id' => ['nullable', $this->inSchool('subjects')],
            'name' => ['required', 'string', 'max:200'],
            'publisher' => ['nullable', 'string', 'max:150'],
            'author' => ['nullable', 'string', 'max:150'],
            'book_number' => ['nullable', 'string', 'max:50', $this->unique('books', 'book_number', $ignore)],
            'rack_number' => ['nullable', 'string', 'max:30'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
        ];
    }

    /**
     * What the form meant by the price: an integer of minor units for the school's money.
     * When the field is empty nothing is meant, so the caller keeps the price it had.
     */
    public function minorPrice(?string $currency): ?int
    {
        $value = $this->input('price');
        if ($value === null || $value === '' || ! is_numeric($value) || $currency === null || $currency === '') {
            return null;
        }

        $decimals = Money::CURRENCIES[strtoupper($currency)] ?? 2;

        return (int) round(((float) $value) * (10 ** $decimals));
    }
}
