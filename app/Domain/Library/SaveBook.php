<?php

namespace App\Domain\Library;

use App\Domain\Settings\RecordAudit;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

/**
 * Adds a book to the library or changes what the school holds of it. What is
 * available is counted from the books that are out, so it is never typed here.
 */
final class SaveBook
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{subject_id?: int|null, name: string, publisher?: string|null, author?: string|null, book_number?: string|null, rack_number?: string|null, quantity: int, price?: int|null}  $data
     */
    public function handle(?Book $book, array $data): Book
    {
        return DB::transaction(function () use ($book, $data): Book {
            $created = $book === null;

            $book ??= new Book;
            $attributes = [
                'subject_id' => $data['subject_id'] ?? null,
                'name' => $data['name'],
                'publisher' => $data['publisher'] ?? null,
                'author' => $data['author'] ?? null,
                'book_number' => $data['book_number'] ?? null,
                'rack_number' => $data['rack_number'] ?? null,
                'quantity' => $data['quantity'],
            ];

            // The price only changes when the form meant one; an empty field keeps the old price.
            if (array_key_exists('price', $data)) {
                $attributes['price'] = $data['price'];
            }

            $book->fill($attributes);
            $book->save();

            $this->audit->handle($created ? 'book.created' : 'book.updated', $book);

            return $book;
        });
    }
}
