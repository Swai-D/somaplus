<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The receipt of one payment, numbered without gaps within the school.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $number
 * @property int $payment_id
 * @property CarbonImmutable $issued_on
 */
class Receipt extends Model
{
    use BelongsToTenant;

    protected $fillable = [];

    protected function casts(): array
    {
        return ['number' => 'integer', 'issued_on' => 'immutable_date'];
    }

    /** The number people see, for example REC-000007. */
    public function reference(): string
    {
        return 'REC-'.str_pad((string) $this->number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * @return BelongsTo<Payment, $this>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
