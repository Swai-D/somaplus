<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * One line of an invoice, copied from a fee item when the invoice was made.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $invoice_id
 * @property string $name
 * @property int $amount
 */
class InvoiceLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [];

    protected function casts(): array
    {
        return ['amount' => 'integer'];
    }
}
