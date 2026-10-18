<?php

/*
|--------------------------------------------------------------------------
| School roles and their permissions
|--------------------------------------------------------------------------
|
| Every school (tenant) gets one role of each kind below when it is created
| (see SyncTenantRoles). Role keys are stored as role names and have English
| and Swahili labels in the roles.php files under lang/en and lang/sw.
|
| `permissions` maps a permission name to the roles that have it. A permission
| is added here only together with the feature that checks it, for example
| 'students.view' => ['owner', 'head_teacher', 'teacher'].
|
| Syncing only ever adds: it creates missing roles and permissions and grants
| missing ones. It never revokes, so a change that removes a permission from a
| role needs a data migration.
|
*/

return [

    'roles' => [
        'owner',
        'head_teacher',
        'teacher',
        'accountant',
        'parent_student',
    ],

    'permissions' => [
        'academics.view' => ['owner', 'head_teacher', 'teacher', 'accountant'],
        'academics.manage' => ['owner', 'head_teacher'],
    ],

];
