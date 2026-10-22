<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Money;
use App\Tenancy\TenantContext;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A book of the school library. The copies the school owns are `quantity`; what is
 * available is counted from the books that are still out, so it is always true.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int|null $subject_id
 * @property string $name
 * @property string|null $publisher
 * @property string|null $author
 * @property string|null $book_number
 * @property string|null $rack_number
 * @property int $quantity
 * @property int|null $price
 */
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'subject_id',
        'name',
        'publisher',
        'author',
        'book_number',
        'rack_number',
        'quantity',
        'price',
    ];

    /**
     * The subject the book belongs to, as the school chose it. A storybook has none.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * The books of this kind that went out, returned and still out.
     *
     * @return HasMany<BookIssue, $this>
     */
    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    /** The copies still out of the library. */
    public function outCount(): int
    {
        return (int) ($this->open_issues_count ?? $this->issues()->whereNull('returned_on')->count());
    }

    /** The copies on the shelf, which the school may hand out. */
    public function availableCount(): int
    {
        return max(0, $this->quantity - $this->outCount());
    }

    /** The price as money of the school, or null when it was not given. */
    public function priceMoney(): ?Money
    {
        if ($this->price === null) {
            return null;
        }

        $currency = TenantContext::instance()->current()?->currency;

        return Money::of($this->price, $currency !== null ? strtoupper($currency) : Money::DEFAULT_CURRENCY);
    }
}
