<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * One fee of a structure, for example "Tuition", with its amount in minor units.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $fee_structure_id
 * @property string $name
 * @property int $amount
 */
class FeeItem extends Model
{
    use BelongsToTenant;

    protected $fillable = ['name', 'amount'];

    protected function casts(): array
    {
        return ['amount' => 'integer'];
    }
}
