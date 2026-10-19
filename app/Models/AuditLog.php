<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * Who did what to which sensitive record. It holds ids only, never names or
 * other personal data, and rows are only added.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int|null $user_id
 * @property string $action
 * @property string $subject_type
 * @property int|null $subject_id
 * @property CarbonImmutable $created_at
 */
class AuditLog extends Model
{
    use BelongsToTenant;

    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id'];

    protected function casts(): array
    {
        return ['created_at' => 'immutable_datetime'];
    }
}
