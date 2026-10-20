<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\ExamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * An exam of a term, marked out of `max_mark`, graded with one grading scale.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $academic_year_id
 * @property int $term_id
 * @property int $grading_scale_id
 * @property string $name
 * @property int $max_mark
 * @property CarbonImmutable|null $locked_at
 * @property int|null $locked_by
 */
class Exam extends Model
{
    /** @use HasFactory<ExamFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['name', 'max_mark'];

    protected function casts(): array
    {
        return ['max_mark' => 'integer', 'locked_at' => 'immutable_datetime'];
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<Term, $this>
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * @return BelongsTo<GradingScale, $this>
     */
    public function gradingScale(): BelongsTo
    {
        return $this->belongsTo(GradingScale::class);
    }

    /**
     * @return HasMany<ExamMark, $this>
     */
    public function marks(): HasMany
    {
        return $this->hasMany(ExamMark::class);
    }
}
