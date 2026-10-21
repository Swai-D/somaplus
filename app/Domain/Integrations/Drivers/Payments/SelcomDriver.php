<?php

namespace App\Domain\Integrations\Drivers\Payments;

use App\Domain\Integrations\Contracts\Driver;

/**
 * Selcom collections (C2B): Selcom's gateway calls the school's lookup, validation and notification addresses when
 * a parent pays a control number from a mobile wallet, bank account, POS or agent. The school gives Selcom the
 * bearer token generated here; every call is refused without it.
 */
class SelcomDriver implements Driver
{
    public const KEY = 'selcom';

    public static function key(): string
    {
        return self::KEY;
    }

    public static function label(): string
    {
        return 'integrations.drivers.selcom';
    }

    public static function fields(): array
    {
        return [
            ['name' => 'bearer_token', 'label' => 'integrations.fields.bearer_token', 'type' => 'password', 'required' => true, 'secret' => true, 'generate' => true, 'min' => 16],
        ];
    }
}
