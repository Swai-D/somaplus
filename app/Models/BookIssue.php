<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\BookIssueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One book that went out to one member of the library, and the day it came back.
 * The copy is still out while `returned_on` is empty.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $book_id
 * @property int $member_id
 * @property CarbonImmutable $issued_on
 * @property CarbonImmutable|null $due_on
 * @property CarbonImmutable|null $returned_on
 */
class BookIssue extends Model
{
    /** @use HasFactory<BookIssueFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['book_id', 'member_id', 'issued_on', 'due_on', 'returned_on'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'due_on' => 'date',
            'returned_on' => 'date',
        ];
    }

    /**
     * The book that went out.
     *
     * @return BelongsTo<Book, $this>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * The member the book went out to.
     *
     * @return BelongsTo<LibraryMember, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class);
    }

    public function isReturned(): bool
    {
        return $this->returned_on !== null;
    }

    /** The day it had to come back has passed, and the copy is still out. */
    public function isOverdue(): bool
    {
        return ! $this->isReturned() && $this->due_on !== null && $this->due_on->lessThan(now());
    }
}
