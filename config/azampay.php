<?php

/*
 | AzamPay endpoints. Each school brings its own AzamPay app, so credentials are saved per school under
 | Settings > Integrations; only the hosts live here. Sandbox hosts come from AzamPay's API reference (and are the
 | ones used by Odessa POS). The production hosts must be confirmed with AzamPay at go-live: override them with the
 | environment variables if they differ.
 */
return [
    'environments' => [
        'sandbox' => [
            'auth' => env('AZAMPAY_SANDBOX_AUTH_URL', 'https://authenticator-sandbox.azampay.co.tz'),
            'api' => env('AZAMPAY_SANDBOX_API_URL', 'https://sandbox.azampay.co.tz'),
        ],
        'production' => [
            'auth' => env('AZAMPAY_PRODUCTION_AUTH_URL', 'https://authenticator.azampay.co.tz'),
            'api' => env('AZAMPAY_PRODUCTION_API_URL', 'https://checkout.azampay.co.tz'),
        ],
    ],

    'timeout' => 20,

    // How long a payer has to approve the prompt on their phone before the request is shown as expired.
    'request_ttl_minutes' => 5,

    // AzamPay's limit for one mobile money checkout, in whole shillings.
    'max_amount' => 5000000,

    // Mobile networks AzamPay accepts in a checkout request.
    'providers' => ['Airtel', 'Tigo', 'Halopesa', 'Azampesa', 'Mpesa'],
];
