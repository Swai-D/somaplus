<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One band of a grading scale: the lowest percentage that earns this grade.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $grading_scale_id
 * @property string $min_mark
 * @property string $grade
 * @property int|null $points
 * @property string|null $remark
 */
class GradingBand extends Model
{
    use BelongsToTenant;

    protected $fillable = ['min_mark', 'grade', 'points', 'remark'];

    protected function casts(): array
    {
        return ['min_mark' => 'decimal:2', 'points' => 'integer'];
    }

    /**
     * @return BelongsTo<GradingScale, $this>
     */
    public function scale(): BelongsTo
    {
        return $this->belongsTo(GradingScale::class, 'grading_scale_id');
    }
}
