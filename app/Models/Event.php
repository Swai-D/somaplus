<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * An event of the school's calendar, such as a parents' meeting or the closing day.
 *
 * @property int $id
 * @property string $tenant_id
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable|null $ends_at
 * @property string $title
 * @property string|null $description
 */
class Event extends Model
{
    use BelongsToTenant;

    protected $fillable = ['starts_at', 'ends_at', 'title', 'description'];

    protected function casts(): array
    {
        return ['starts_at' => 'immutable_datetime', 'ends_at' => 'immutable_datetime'];
    }
}
