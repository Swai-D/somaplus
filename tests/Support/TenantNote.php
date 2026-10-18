<?php

namespace Tests\Support;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TenantNote extends Model
{
    use BelongsToTenant;

    protected $table = 'tenant_notes';

    protected $fillable = ['body'];
}
