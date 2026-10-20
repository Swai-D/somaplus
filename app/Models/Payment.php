<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Money received for an invoice. Never edited or deleted: a mistake is undone with a reversal.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $invoice_id
 * @property int $amount
 * @property string $currency
 * @property string $method
 * @property string $source
 * @property string|null $reference
 * @property string|null $control_number
 * @property string $idempotency_key
 * @property string|null $provider_reference
 * @property CarbonImmutable $paid_on
 * @property int|null $recorded_by
 */
class Payment extends Model
{
    use BelongsToTenant;

    public const METHODS = ['cash', 'bank', 'mobile_money'];

    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_INTEGRATION = 'integration';

    protected $fillable = [];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'paid_on' => 'immutable_date'];
    }

    public function money(): Money
    {
        return Money::of($this->amount, $this->currency);
    }

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @return HasOne<Receipt, $this>
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    /**
     * @return HasOne<PaymentReversal, $this>
     */
    public function reversal(): HasOne
    {
        return $this->hasOne(PaymentReversal::class);
    }

    public function isReversed(): bool
    {
        return $this->reversal()->exists();
    }
}
