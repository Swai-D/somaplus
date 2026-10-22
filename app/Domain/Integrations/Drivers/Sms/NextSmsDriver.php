<?php

namespace App\Domain\Integrations\Drivers\Sms;

use App\Domain\Integrations\Contracts\Driver;

/** Texts through the school's own NextSMS account (messaging-service.co.tz): the school pays NextSMS directly. */
class NextSmsDriver implements Driver
{
    public const KEY = 'nextsms';

    public static function key(): string
    {
        return self::KEY;
    }

    public static function label(): string
    {
        return 'integrations.drivers.nextsms';
    }

    public static function fields(): array
    {
        return [
            ['name' => 'username', 'label' => 'integrations.fields.sms_username', 'type' => 'text', 'required' => true],
            ['name' => 'password', 'label' => 'integrations.fields.sms_password', 'type' => 'password', 'required' => true, 'secret' => true],
            ['name' => 'sender_id', 'label' => 'integrations.fields.sms_sender_id', 'type' => 'text', 'required' => true, 'min' => 3, 'max' => 11],
        ];
    }
}
