<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A student's invoice for one fee structure. Lines and total are a snapshot taken when it was made.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $number
 * @property int $student_id
 * @property int $academic_year_id
 * @property int $term_id
 * @property int $fee_structure_id
 * @property int $school_class_id
 * @property int|null $stream_id
 * @property string $currency
 * @property int $total
 * @property string|null $control_number
 * @property CarbonImmutable $issued_on
 */
class Invoice extends Model
{
    use BelongsToTenant;

    /** Only the control number can be set afterwards (see SetControlNumber); everything else is fixed. */
    protected $fillable = [];

    protected function casts(): array
    {
        return ['total' => 'integer', 'number' => 'integer', 'issued_on' => 'immutable_date'];
    }

    /** The number people see, for example INV-000012. */
    public function reference(): string
    {
        return 'INV-'.str_pad((string) $this->number, 6, '0', STR_PAD_LEFT);
    }

    public function totalMoney(): Money
    {
        return Money::of($this->total, $this->currency);
    }

    /**
     * @return HasMany<InvoiceLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class)->orderBy('id');
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<FeeStructure, $this>
     */
    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
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
}
