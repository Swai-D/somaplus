<?php

return [
    'intro' => 'Unganisha shule yako na huduma za nje. Kila kitu kimezimwa hadi uwashe na kuhifadhi maelezo hapa.',
    'saved' => 'Imehifadhiwa.',
    'save' => 'Hifadhi',
    'enabled' => 'Imewashwa',
    'provider' => 'Mtoa huduma',
    'none' => 'Bado hakuna miunganisho inayopatikana.',
    'secret_saved' => 'Imehifadhiwa. Acha wazi kuiacha kama ilivyo.',
    'secret_generate' => 'Acha wazi na Somaplus itakutengenezea.',
    'generated_title' => 'Siri yako mpya. Inakili sasa.',
    'generated_hint' => 'Inaonyeshwa mara hii tu na haiwezi kuonekana tena. Ukiipoteza, hifadhi tena ukiacha sehemu wazi ili kutengeneza nyingine.',
    'channels' => [
        'payments' => [
            'title' => 'Taarifa za malipo (benki, GePG, pesa za simu)',
            'hint' => 'Benki au mtoa huduma wako anaiambia Somaplus namba ya malipo inapolipwa. Malipo na stakabadhi yake hurekodiwa kiotomatiki.',
        ],
    ],
    'drivers' => [
        'signed_webhook' => 'Taarifa ya mtandao iliyosainiwa',
    ],
    'fields' => [
        'webhook_secret' => 'Siri ya pamoja',
    ],
    'webhook' => [
        'title' => 'Anwani ya kumpa benki au mtoa huduma wako',
        'how' => 'Tuma ombi la POST lenye mwili wa JSON na vichwa viwili: X-Somaplus-Timestamp (sekunde za Unix) na X-Somaplus-Signature (HMAC-SHA256 ya hex ya "timestamp.body" kwa siri ya pamoja). Taarifa za zaidi ya dakika tano zinakataliwa.',
        'example' => '{"control_number":"991234567890","amount":"50000","currency":"TZS","reference":"BANK-0001","paid_on":"2026-10-21","method":"bank"}',
    ],
    'notices' => [
        'title' => 'Taarifa za malipo za karibuni',
        'hint' => 'Taarifa 20 za mwisho zilizopokelewa. Taarifa yenye namba ya malipo isiyojulikana husubiri na hutumika inapotumwa tena baada ya namba hiyo kuwekwa kwenye ankara.',
        'none' => 'Bado hakuna taarifa iliyopokelewa.',
        'received' => 'Imepokelewa',
        'reference' => 'Rejea ya mtoa huduma',
        'statuses' => [
            'applied' => 'Imerekodiwa',
            'duplicate' => 'Ilikwisharekodiwa',
            'unmatched' => 'Ankara haikupatikana',
            'rejected' => 'Imekataliwa',
        ],
        'reasons' => [
            'unknown_control_number' => 'namba ya malipo haijulikani',
            'bad_amount_or_date' => 'kiasi au tarehe si sahihi',
            'currency_mismatch' => 'sarafu hailingani na ya shule',
            'conflicting_reference' => 'rejea imeshatumika kwa malipo mengine',
        ],
    ],
    'errors' => [
        'driver_required' => 'Chagua mtoa huduma kabla ya kuwasha.',
        'field_required' => 'Maelezo haya yanahitajika kabla ya kuwasha.',
    ],
];
