<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * A notice on the school's notice board, such as an opening day or a uniform change.
 *
 * @property int $id
 * @property string $tenant_id
 * @property CarbonImmutable $day
 * @property string $title
 * @property string|null $description
 */
class Notice extends Model
{
    use BelongsToTenant;

    protected $fillable = ['day', 'title', 'description'];

    protected function casts(): array
    {
        return ['day' => 'immutable_date'];
    }
}
