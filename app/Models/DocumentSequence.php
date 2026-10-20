<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * The last number given out for a kind of document (invoice, later receipt) in one school.
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $type
 * @property int $last_number
 */
class DocumentSequence extends Model
{
    use BelongsToTenant;

    protected $fillable = ['type', 'last_number'];
}
