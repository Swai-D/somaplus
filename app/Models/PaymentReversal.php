<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Undoes a payment without removing it: the payment and its receipt stay, and the balance ignores it.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $payment_id
 * @property string $reason
 * @property int|null $reversed_by
 * @property CarbonImmutable $reversed_on
 */
class PaymentReversal extends Model
{
    use BelongsToTenant;

    protected $fillable = [];

    protected function casts(): array
    {
        return ['reversed_on' => 'immutable_date'];
    }

    /**
     * @return BelongsTo<Payment, $this>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
