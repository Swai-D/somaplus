<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A fee reminder text to one guardian about one invoice, and how it went. The phone number and the text are not kept.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $invoice_id
 * @property int $guardian_id
 * @property int|null $sent_by
 * @property string $kind
 * @property string $locale
 * @property CarbonImmutable $day
 * @property string $status
 * @property string|null $provider_reference
 * @property string|null $error
 * @property CarbonImmutable|null $sent_at
 * @property CarbonImmutable $created_at
 */
class SmsMessage extends Model
{
    use BelongsToTenant;

    public const KIND_REMINDER = 'reminder';

    public const QUEUED = 'queued';

    public const SENT = 'sent';

    public const FAILED = 'failed';

    public const CANCELLED = 'cancelled';

    protected $fillable = [];

    protected function casts(): array
    {
        return ['day' => 'immutable_date', 'sent_at' => 'immutable_datetime'];
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return BelongsTo<Guardian, $this> */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }
}
