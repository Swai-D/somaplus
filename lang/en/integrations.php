<?php

return [
    'intro' => 'Connect your school to outside services. Everything is off until you switch it on and save the details here.',
    'saved' => 'Saved.',
    'save' => 'Save',
    'enabled' => 'Switched on',
    'provider' => 'Provider',
    'none' => 'No integrations are available yet.',
    'secret_saved' => 'Saved. Leave empty to keep it.',
    'secret_generate' => 'Leave empty and Somaplus will make one for you.',
    'generated_title' => 'Your new secret. Copy it now.',
    'generated_hint' => 'It is shown only this once and cannot be seen again. If you lose it, save again with an empty field to make a new one.',
    'channels' => [
        'payments' => [
            'title' => 'Payment notices (bank, GePG, mobile money)',
            'hint' => 'Your bank or payment provider tells Somaplus when a control number is paid. The payment and its receipt are then recorded automatically.',
        ],
    ],
    'drivers' => [
        'signed_webhook' => 'Signed web notice',
    ],
    'fields' => [
        'webhook_secret' => 'Shared secret',
    ],
    'webhook' => [
        'title' => 'Address to give your bank or provider',
        'how' => 'Send a POST request with a JSON body and two headers: X-Somaplus-Timestamp (Unix seconds) and X-Somaplus-Signature (hex HMAC-SHA256 of "timestamp.body" using the shared secret). Notices older than five minutes are refused.',
        'example' => '{"control_number":"991234567890","amount":"50000","currency":"TZS","reference":"BANK-0001","paid_on":"2026-10-21","method":"bank"}',
    ],
    'notices' => [
        'title' => 'Latest payment notices',
        'hint' => 'The last 20 notices received. A notice with an unknown control number waits and is applied when it is sent again after the control number is set on an invoice.',
        'none' => 'No notice received yet.',
        'received' => 'Received',
        'reference' => 'Provider reference',
        'statuses' => [
            'applied' => 'Recorded',
            'duplicate' => 'Already recorded',
            'unmatched' => 'No invoice found',
            'rejected' => 'Rejected',
        ],
        'reasons' => [
            'unknown_control_number' => 'unknown control number',
            'bad_amount_or_date' => 'bad amount or date',
            'currency_mismatch' => 'currency does not match the school',
            'conflicting_reference' => 'reference already used for another payment',
        ],
    ],
    'errors' => [
        'driver_required' => 'Choose a provider before switching this on.',
        'field_required' => 'This detail is required before switching this on.',
    ],
];
