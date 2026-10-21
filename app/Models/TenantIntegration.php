<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * The provider a school chose for one channel, its settings and its secrets (encrypted, never shown again).
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $channel
 * @property string $driver
 * @property bool $enabled
 * @property array<string, mixed>|null $settings
 * @property array<string, mixed>|null $secrets
 * @property int|null $updated_by
 */
class TenantIntegration extends Model
{
    use BelongsToTenant;

    protected $fillable = [];

    protected $hidden = ['secrets'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean', 'settings' => 'array', 'secrets' => 'encrypted:array'];
    }
}
