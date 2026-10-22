<?php

use App\Domain\Integrations\Drivers\Payments\AzamPayDriver;
use App\Domain\Integrations\Drivers\Payments\GepgDriver;
use App\Domain\Integrations\Drivers\Payments\SelcomDriver;
use App\Domain\Integrations\Drivers\Payments\SignedWebhookDriver;
use App\Domain\Integrations\Drivers\Sms\NextSmsDriver;

/*
|--------------------------------------------------------------------------
| Integrations a school can plug in from Settings
|--------------------------------------------------------------------------
|
| Each channel lists the drivers (providers) it offers. A driver describes its own configuration
| fields, and Settings > Integrations builds its form from that description, so adding a provider
| is one driver class and one line here. Everything is off until the school chooses a driver, fills
| in its settings and switches it on; secrets are stored encrypted and never shown again.
| A channel with no drivers is not shown.
|
*/

return [

    'channels' => [
        'payments' => [
            'drivers' => [SignedWebhookDriver::class, GepgDriver::class, SelcomDriver::class, AzamPayDriver::class],
        ],
        'sms' => [
            'drivers' => [NextSmsDriver::class],
        ],
    ],

];
