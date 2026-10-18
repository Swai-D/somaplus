<?php

namespace App\Models;

use Database\Factories\SubscriptionPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $key
 * @property string $name
 * @property array<int, string> $features
 * @property int $sort_order
 */
class SubscriptionPlan extends Model
{
    /** @use HasFactory<SubscriptionPlanFactory> */
    use HasFactory;

    protected $fillable = ['key', 'name', 'features', 'sort_order'];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function allows(string $feature): bool
    {
        return in_array($feature, $this->features, true);
    }

    /**
     * @return HasMany<Tenant, $this>
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
