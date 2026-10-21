<?php

namespace App\Domain\Integrations\Drivers\Payments;

use App\Domain\Integrations\Contracts\Driver;

/**
 * Payment notices from a bank, aggregator or any system that can send a signed web request. The school
 * gives that system the webhook address shown in Settings and the shared secret; each notice is checked
 * with an HMAC signature before any money is recorded (see ReceivePaymentNotification).
 */
class SignedWebhookDriver implements Driver
{
    public const KEY = 'signed_webhook';

    public static function key(): string
    {
        return self::KEY;
    }

    public static function label(): string
    {
        return 'integrations.drivers.signed_webhook';
    }

    public static function fields(): array
    {
        return [
            ['name' => 'webhook_secret', 'label' => 'integrations.fields.webhook_secret', 'type' => 'password', 'required' => true, 'secret' => true, 'generate' => true, 'min' => 16],
        ];
    }
}
