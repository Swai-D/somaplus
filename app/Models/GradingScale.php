<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\GradingScaleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A grading scale of a school: a list of bands (lowest percentage => grade).
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $name
 */
class GradingScale extends Model
{
    /** @use HasFactory<GradingScaleFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['name'];

    /**
     * Lowest band first.
     *
     * @return HasMany<GradingBand, $this>
     */
    public function bands(): HasMany
    {
        return $this->hasMany(GradingBand::class)->orderBy('min_mark');
    }

    /**
     * @return HasMany<Exam, $this>
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /** A scale that an exam uses is never changed, so past grades stay the same. */
    public function isUsed(): bool
    {
        return $this->exams()->exists();
    }
}
