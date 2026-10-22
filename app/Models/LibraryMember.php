<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\LibraryMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A member of the library: one pupil of the school with the card the school gave them.
 * A pupil is a member at most once, which the database keeps so, and their phone comes
 * from their guardian, never from here.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $student_id
 * @property string $card_number
 * @property CarbonImmutable $joined_on
 */
class LibraryMember extends Model
{
    /** @use HasFactory<LibraryMemberFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['student_id', 'card_number', 'joined_on'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'joined_on' => 'date',
        ];
    }

    /**
     * The pupil the card belongs to.
     *
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * The books this member borrowed and brought back or still has.
     *
     * @return HasMany<BookIssue, $this>
     */
    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'member_id');
    }

    /** The pupil's name, taken from the pupil of the school. */
    public function fullName(): string
    {
        return $this->student->fullName();
    }

    /** The books of this member that are still out. */
    public function outCount(): int
    {
        return (int) ($this->open_issues_count ?? $this->issues()->whereNull('returned_on')->count());
    }
}
