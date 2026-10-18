<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\TermFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $tenant_id
 * @property int $academic_year_id
 * @property string $name
 * @property Carbon $starts_on
 * @property Carbon $ends_on
 */
class Term extends Model
{
    /** @use HasFactory<TermFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['name', 'starts_on', 'ends_on'];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
