<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * The contact details a school prints at the top of its receipts and report cards. A school with no row prints
 * only its name.
 *
 * @property int $id
 * @property string $tenant_id
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 */
class SchoolProfile extends Model
{
    use BelongsToTenant;

    protected $fillable = ['phone', 'email', 'address'];

    /** The school's profile, or an empty unsaved one. */
    public static function current(): self
    {
        return self::query()->first() ?? new self;
    }

    /**
     * The non-empty contact lines, in print order.
     *
     * @return list<string>
     */
    public function lines(): array
    {
        return array_values(array_filter([$this->address, $this->phone, $this->email], fn ($line) => is_string($line) && $line !== ''));
    }
}
