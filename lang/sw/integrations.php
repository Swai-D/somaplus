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
        'gepg' => 'GePG (Lango la Malipo ya Kielektroniki la Serikali)',
        'selcom' => 'Selcom (pochi za simu, benki, mawakala)',
        'signed_webhook' => 'Taarifa ya mtandao iliyosainiwa',
    ],
    'fields' => [
        'sp_code' => 'Msimbo wa mtoa huduma (SpCode)',
        'sp_sys_id' => 'Kitambulisho cha mfumo (SpSysId)',
        'gepg_certificate' => 'Cheti cha umma cha GePG (PEM)',
        'signing_key' => 'Ufunguo wako binafsi wa kusaini majibu (PEM)',
        'bearer_token' => 'Tokeni ya Bearer kwa Selcom',
        'webhook_secret' => 'Siri ya pamoja',
    ],
    'webhook' => [
        'title' => 'Anwani ya kumpa benki au mtoa huduma wako',
        'how' => 'Tuma ombi la POST lenye mwili wa JSON na vichwa viwili: X-Somaplus-Timestamp (sekunde za Unix) na X-Somaplus-Signature (HMAC-SHA256 ya hex ya "timestamp.body" kwa siri ya pamoja). Taarifa za zaidi ya dakika tano zinakataliwa.',
        'example' => '{"control_number":"991234567890","amount":"50000","currency":"TZS","reference":"BANK-0001","paid_on":"2026-10-21","method":"bank"}',
    ],
    'gepg' => [
        'title' => 'Anwani ya kumpa GePG',
        'how' => 'Mpe GePG anwani hii kama anwani ya kupokea taarifa za malipo, pamoja na cheti chako cha umma, kisha bandika hapa cheti cha umma cha GePG na ufunguo binafsi wa cheti chako. Somaplus huthibitisha kila taarifa kwa cheti cha GePG na kujibu kwa uthibitisho uliosainiwa. Control number hutolewa na GePG; ziandike kwenye ankara husika (Fedha > Ankara) ili taarifa ziipate.',
    ],
    'selcom' => [
        'title' => 'Anwani za kumpa Selcom',
        'how' => 'Mpe Selcom anwani hizi tatu (lookup, validation, notification) na tokeni ya bearer iliyo juu. Mzazi analipa control number ya ankara kama rejea ya malipo; Somaplus inamwonyesha Selcom jina la mwanafunzi, inapokea kiasi na kurekodi malipo pamoja na risiti. Selcom hulipa kwa TZS, kwa hiyo sarafu ya shule lazima iwe TZS. Andika kila control number kwenye ankara yake (Fedha > Ankara).',
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
