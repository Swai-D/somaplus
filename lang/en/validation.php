<?php

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be text.',
    'email' => 'The :attribute must be a valid email address.',
    'boolean' => 'The :attribute field must be true or false.',
    'in' => 'The selected :attribute is not valid.',
    'date' => 'The :attribute must be a valid date.',
    'after_or_equal' => 'The :attribute must be on or after :date.',
    'unique' => 'This :attribute is already in use.',
    'integer' => 'The :attribute must be a whole number.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'max' => [
        'numeric' => 'The :attribute must not be more than :max.',
        'string' => 'The :attribute must not be longer than :max characters.',
    ],
    'attributes' => [
        'name' => 'name',
        'starts_on' => 'start date',
        'ends_on' => 'end date',
        'code' => 'code',
        'sort_order' => 'order',
        'email' => 'email address',
        'password' => 'password',
        'locale' => 'language',
        'token' => 'reset link',
    ],
];
