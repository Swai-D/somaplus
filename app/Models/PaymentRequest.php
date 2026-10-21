<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A mobile-money prompt sent to a payer's phone for one invoice, and how it ended. The payment itself is a normal
 * `Payment` (source integration) created when the provider confirms.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $invoice_id
 * @property string $idempotency_key
 * @property string $reference
 * @property string $driver
 * @property string $provider
 * @property int $amount
 * @property string $currency
 * @property string $status
 * @property string|null $failure_reason
 * @property string|null $provider_reference
 * @property int|null $payment_id
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $completed_at
 * @property CarbonImmutable $created_at
 */
class PaymentRequest extends Model
{
    use BelongsToTenant;

    public const PENDING = 'pending';

    public const SUCCEEDED = 'succeeded';

    public const FAILED = 'failed';

    protected $fillable = [];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'expires_at' => 'immutable_datetime', 'completed_at' => 'immutable_datetime'];
    }

    /** What to show: a prompt nobody answered in time reads as expired (a late success is still honoured). */
    public function displayStatus(): string
    {
        return $this->status === self::PENDING && $this->expires_at->isPast() ? 'expired' : $this->status;
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
