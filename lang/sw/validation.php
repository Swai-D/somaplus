<?php

return [
    'required' => 'Sehemu ya :attribute inahitajika.',
    'string' => ':attribute lazima iwe maandishi.',
    'email' => ':attribute lazima iwe barua pepe halali.',
    'boolean' => 'Sehemu ya :attribute lazima iwe ndiyo au hapana.',
    'in' => ':attribute iliyochaguliwa si halali.',
    'date' => ':attribute lazima iwe tarehe halali.',
    'after_or_equal' => ':attribute lazima iwe tarehe ile au baada ya :date.',
    'unique' => ':attribute hii tayari inatumika.',
    'integer' => ':attribute lazima iwe nambari kamili.',
    'confirmed' => 'Uthibitisho wa :attribute haulingani.',
    'min' => [
        'numeric' => ':attribute iwe angalau :min.',
        'string' => ':attribute iwe angalau herufi :min.',
    ],
    'max' => [
        'numeric' => ':attribute isizidi :max.',
        'string' => ':attribute isizidi herufi :max.',
    ],
    'attributes' => [
        'name' => 'jina',
        'starts_on' => 'tarehe ya kuanza',
        'ends_on' => 'tarehe ya kuisha',
        'code' => 'msimbo',
        'sort_order' => 'mpangilio',
        'email' => 'barua pepe',
        'password' => 'nenosiri',
        'locale' => 'lugha',
        'token' => 'kiungo cha kubadilisha',
    ],
];
