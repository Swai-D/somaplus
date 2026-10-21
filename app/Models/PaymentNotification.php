<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * A verified payment notice from a provider and what became of it (applied, unmatched or rejected).
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $provider_reference
 * @property string|null $control_number
 * @property int|null $amount
 * @property string|null $currency
 * @property string $status
 * @property string|null $reason
 * @property int|null $payment_id
 * @property CarbonImmutable $received_at
 */
class PaymentNotification extends Model
{
    use BelongsToTenant;

    protected $fillable = [];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'received_at' => 'immutable_datetime'];
    }
}
