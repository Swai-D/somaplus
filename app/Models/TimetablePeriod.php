<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\TimetablePeriodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A lesson slot of the school's day, or a break.
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $name
 * @property string $starts_at HH:MM:SS
 * @property string $ends_at HH:MM:SS
 * @property bool $is_break
 */
class TimetablePeriod extends Model
{
    /** @use HasFactory<TimetablePeriodFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['name', 'starts_at', 'ends_at', 'is_break'];

    protected function casts(): array
    {
        return ['is_break' => 'boolean'];
    }

    /**
     * @return HasMany<TimetableEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class);
    }

    /** "08:00" */
    public function start(): string
    {
        return substr((string) $this->starts_at, 0, 5);
    }

    /** "08:40" */
    public function end(): string
    {
        return substr((string) $this->ends_at, 0, 5);
    }

    /** "08:00 - 08:40" */
    public function range(): string
    {
        return $this->start().' - '.$this->end();
    }
}
