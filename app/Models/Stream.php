<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\StreamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A stream (parallel group) of one class, for example "A" in "Form 1".
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $school_class_id
 * @property string $name
 */
class Stream extends Model
{
    /** @use HasFactory<StreamFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['name'];

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
