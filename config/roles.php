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
| `implies` lists, for a permission, the permissions a person needs with it for its pages to work
| (changing students needs looking at them). A custom role always gets them together.
| `custom_excluded` lists permissions only the five default roles can have: a custom role never
| gets them, because they would let its holder change roles, users, integrations and more.
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
        'communication.view' => ['owner', 'head_teacher', 'teacher', 'accountant'],
        'communication.manage' => ['owner', 'head_teacher'],
        'academics.view' => ['owner', 'head_teacher', 'teacher', 'accountant'],
        'academics.manage' => ['owner', 'head_teacher'],
        'students.view' => ['owner', 'head_teacher', 'teacher', 'accountant'],
        'students.manage' => ['owner', 'head_teacher'],
        'teachers.view' => ['owner', 'head_teacher'],
        'teachers.manage' => ['owner', 'head_teacher'],
        'library.view' => ['owner', 'head_teacher', 'teacher'],
        'library.manage' => ['owner', 'head_teacher'],
        'attendance.manage' => ['owner', 'head_teacher', 'teacher'],
        'timetable.view' => ['owner', 'head_teacher', 'teacher'],
        'timetable.manage' => ['owner', 'head_teacher'],
        'grades.manage' => ['owner', 'head_teacher'],
        'exams.view' => ['owner', 'head_teacher', 'teacher'],
        'exams.manage' => ['owner', 'head_teacher'],
        'marks.enter' => ['owner', 'head_teacher', 'teacher'],
        'results.view' => ['owner', 'head_teacher', 'teacher'],
        'fees.view' => ['owner', 'head_teacher', 'accountant'],
        'fees.manage' => ['owner', 'accountant'],
        'payments.record' => ['owner', 'accountant'],
        'payments.reverse' => ['owner'],
        'reminders.send' => ['owner', 'accountant'],
        'settings.manage' => ['owner'],
        'family.portal' => ['parent_student'],
    ],

    'implies' => [
        'academics.manage' => ['academics.view'],
        'students.manage' => ['students.view'],
        'teachers.manage' => ['teachers.view'],
        'library.manage' => ['library.view'],
        'timetable.manage' => ['timetable.view'],
        'exams.manage' => ['exams.view'],
        'marks.enter' => ['exams.view'],
        'communication.manage' => ['communication.view'],
        'fees.manage' => ['fees.view'],
        'payments.record' => ['fees.view'],
        'payments.reverse' => ['fees.view'],
    ],

    // `family.portal` belongs to the parent role only: it opens a parent's own children, not the school's data.
    'custom_excluded' => ['settings.manage', 'family.portal'],

];
