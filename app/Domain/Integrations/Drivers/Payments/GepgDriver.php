<?php

namespace App\Domain\Integrations\Drivers\Payments;

use App\Domain\Integrations\Contracts\Driver;

/**
 * Payment notices from GePG (Government e-Payment Gateway). GePG posts a signed XML `gepgPmtSpInfo` to the
 * callback address; Somaplus checks the signature with GePG's public certificate, records the payment by
 * control number and answers with a signed acknowledgement (code 7101). Only this inbound part is covered:
 * the school's control numbers are issued by GePG and typed on the invoice.
 */
class GepgDriver implements Driver
{
    public const KEY = 'gepg';

    public static function key(): string
    {
        return self::KEY;
    }

    public static function label(): string
    {
        return 'integrations.drivers.gepg';
    }

    public static function fields(): array
    {
        return [
            ['name' => 'sp_code', 'label' => 'integrations.fields.sp_code', 'type' => 'text', 'required' => true, 'max' => 5],
            ['name' => 'sp_sys_id', 'label' => 'integrations.fields.sp_sys_id', 'type' => 'text', 'required' => true, 'max' => 10],
            ['name' => 'gepg_certificate', 'label' => 'integrations.fields.gepg_certificate', 'type' => 'textarea', 'required' => true, 'max' => 8000],
            ['name' => 'signing_key', 'label' => 'integrations.fields.signing_key', 'type' => 'textarea', 'required' => true, 'secret' => true, 'max' => 8000],
        ];
    }
}
