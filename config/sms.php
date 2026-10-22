<?php

/*
 | SMS. Each school brings its own NextSMS account (Settings > Integrations > SMS), so credentials are saved per
 | school; only the address, the country and the limits live here. NextSMS: https://messaging-service.co.tz
 */
return [
    'country_code' => '255',

    'nextsms' => [
        'base_url' => env('NEXTSMS_BASE_URL', 'https://messaging-service.co.tz'),
        'send_path' => env('NEXTSMS_SEND_PATH', '/api/sms/v1/text/single'),
        'timeout' => 15,
    ],

    // Most invoices a person can send reminders for at once.
    'max_batch' => 100,
];
