<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * How a school gives admission numbers: typed by hand, or made by Somaplus from a format
 * (prefix, year, running number). A school with no row types them by hand.
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $mode
 * @property string $prefix
 * @property string $separator
 * @property string $year_format
 * @property int $digits
 */
class AdmissionSetting extends Model
{
    use BelongsToTenant;

    public const MANUAL = 'manual';

    public const AUTO = 'auto';

    public const YEAR_NONE = 'none';

    public const YEAR_FULL = 'YYYY';

    public const YEAR_SHORT = 'YY';

    protected $fillable = ['mode', 'prefix', 'separator', 'year_format', 'digits'];

    /** The school's setting, or the unsaved default (manual) when it never chose. */
    public static function current(): self
    {
        return self::query()->first() ?? new self(['mode' => self::MANUAL, 'prefix' => '', 'separator' => '-', 'year_format' => self::YEAR_FULL, 'digits' => 4]);
    }

    public function isAuto(): bool
    {
        return $this->mode === self::AUTO;
    }
}
