<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\SchoolClassFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A class level of the school (for example "Form 1" or "Standard 4").
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $name
 * @property int $sort_order
 */
class SchoolClass extends Model
{
    /** @use HasFactory<SchoolClassFactory> */
    use BelongsToTenant, HasFactory;

    protected $table = 'school_classes';

    protected $fillable = ['name', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    /**
     * @return HasMany<Stream, $this>
     */
    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class)->orderBy('name');
    }
}
