<?php

namespace App\Models;

use App\Domain\Settings\SyncTenantRoles;
use App\Enums\SubscriptionStatus;
use App\Support\Money;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A school. Tenants are not themselves tenant-scoped: they are what scoping
 * is based on.
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $domain
 * @property int $subscription_plan_id
 * @property SubscriptionStatus $subscription_status
 * @property Carbon|null $grace_ends_at
 * @property string $currency
 */
class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'subscription_plan_id',
        'subscription_status',
        'grace_ends_at',
    ];

    /** @var array<string, mixed> */
    protected $attributes = ['currency' => Money::DEFAULT_CURRENCY];

    protected static function booted(): void
    {
        // Every school starts with the same set of roles.
        static::created(fn (self $tenant) => app(SyncTenantRoles::class)->handle($tenant));
    }

    protected function casts(): array
    {
        return [
            'subscription_status' => SubscriptionStatus::class,
            'grace_ends_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<SubscriptionPlan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isReadOnly(): bool
    {
        return $this->subscription_status === SubscriptionStatus::ReadOnly;
    }
}
