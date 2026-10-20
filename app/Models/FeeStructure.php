<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\FeeStructureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The fees of one class in one term. Once an invoice exists from it, it never changes.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $academic_year_id
 * @property int $term_id
 * @property int $school_class_id
 * @property string $name
 */
class FeeStructure extends Model
{
    /** @use HasFactory<FeeStructureFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['name'];

    /**
     * @return HasMany<FeeItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(FeeItem::class)->orderBy('id');
    }

    /**
     * @return HasMany<Invoice, $this>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return BelongsTo<Term, $this>
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function isUsed(): bool
    {
        return $this->invoices()->exists();
    }

    /** Sum of the items, in minor units. */
    public function total(): int
    {
        return (int) $this->items->sum('amount');
    }
}
