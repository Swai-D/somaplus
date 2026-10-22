<?php

/*
|--------------------------------------------------------------------------
| Subscription plans (seed data only)
|--------------------------------------------------------------------------
|
| The source of truth is the `subscription_plans` table. This file only seeds
| plans that do not exist yet (see PlanSeeder, which never overwrites a plan).
| Changing the features of an existing plan needs a data migration.
|
| Feature keys are what `plan:<feature>` route middleware and
| `Plans::current()->allows('<feature>')` check. A key is added here only when
| a feature that checks it exists.
|
*/

return [

    'plans' => [
        'basic' => [
            'name' => 'Basic',
            'sort_order' => 1,
            'features' => [],
        ],
        'medium' => [
            'name' => 'Medium',
            'sort_order' => 2,
            'features' => [],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'sort_order' => 3,
            'features' => ['custom_roles'],
        ],
    ],

];
