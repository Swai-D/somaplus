<?php

/*
|--------------------------------------------------------------------------
| Sidebar navigation
|--------------------------------------------------------------------------
|
| The sidebar is built from this file by App\Support\Menu. An entry is shown
| only when everything it asks for is true; nothing is shown "for later".
|
|   label       translation key (the menu file in lang)
|   icon        template icon classes, e.g. "ti ti-school"
|   route       named route; an entry whose route does not exist is hidden
|   permission  optional; the signed-in person must hold it in their school
|   feature     optional; the school's plan must include it
|   active      optional list of route name patterns that also mark the entry as
|               active (for example "academics.years.*")
|   scope       "school" (default), "central" (platform admins) or "both"
|   children    optional list of entries; the parent is shown only when at
|               least one child is, and has no route of its own
|
| An entry is added here together with the route and the feature it opens.
|
*/

return [

    'sections' => [
        [
            'label' => 'menu.sections.main',
            'items' => [
                [
                    'label' => 'menu.dashboard',
                    'icon' => 'ti ti-layout-grid',
                    'scope' => 'both',
                    'children' => [
                        [
                            'label' => 'menu.dashboard_school',
                            'route' => 'dashboard',
                            'scope' => 'both',
                        ],
                        [
                            'label' => 'menu.dashboard_teacher',
                            'route' => 'teacher.dashboard',
                            'permission' => 'marks.enter',
                        ],
                    ],
                ],
            ],
        ],
        [
            'label' => 'menu.sections.academics',
            'items' => [
                [
                    'label' => 'menu.students',
                    'icon' => 'ti ti-school',
                    'children' => [
                        [
                            'label' => 'menu.student_list',
                            'route' => 'students.index',
                            'permission' => 'students.view',
                            'active' => ['students.index', 'students.search', 'students.show', 'students.edit'],
                        ],
                        [
                            'label' => 'menu.add_student',
                            'route' => 'students.create',
                            'permission' => 'students.manage',
                            'active' => ['students.create'],
                        ],
                        [
                            'label' => 'menu.import_students',
                            'route' => 'students.import',
                            'permission' => 'students.manage',
                            'active' => ['students.import', 'students.import.store', 'students.import.template'],
                        ],
                        [
                            'label' => 'menu.id_cards',
                            'route' => 'students.id-cards',
                            'permission' => 'students.manage',
                            'active' => ['students.id-cards', 'students.id-cards.print', 'students.id-card'],
                        ],
                    ],
                ],
                [
                    'label' => 'menu.guardians',
                    'icon' => 'ti ti-users',
                    'children' => [
                        [
                            'label' => 'menu.add_guardian',
                            'route' => 'guardians.add',
                            'permission' => 'students.manage',
                            'active' => ['guardians.add'],
                        ],
                        [
                            'label' => 'menu.guardian_list',
                            'route' => 'guardians.index',
                            'permission' => 'students.view',
                            'active' => ['guardians.index', 'guardians.search', 'guardians.show'],
                        ],
                    ],
                ],
                [
                    'label' => 'menu.attendance',
                    'icon' => 'ti ti-checklist',
                    'children' => [
                        [
                            'label' => 'menu.take_attendance',
                            'route' => 'attendance.index',
                            'permission' => 'attendance.manage',
                            'active' => ['attendance.index', 'attendance.store'],
                        ],
                        [
                            'label' => 'menu.attendance_summary',
                            'route' => 'attendance.summary',
                            'permission' => 'attendance.manage',
                            'active' => ['attendance.summary'],
                        ],
                    ],
                ],
                [
                    'label' => 'menu.exams',
                    'icon' => 'ti ti-file-certificate',
                    'children' => [
                        [
                            'label' => 'menu.exam_list',
                            'route' => 'exams.index',
                            'permission' => 'exams.view',
                            'active' => ['exams.*'],
                        ],
                        [
                            'label' => 'menu.grading_scales',
                            'route' => 'grading-scales.index',
                            'permission' => 'exams.view',
                            'active' => ['grading-scales.*'],
                        ],
                    ],
                ],
                [
                    'label' => 'menu.academic_years',
                    'icon' => 'ti ti-calendar-event',
                    'route' => 'academics.years.index',
                    'permission' => 'academics.view',
                    'active' => ['academics.years.*', 'academics.terms.*'],
                ],
                [
                    'label' => 'menu.classes',
                    'icon' => 'ti ti-chalkboard',
                    'children' => [
                        [
                            'label' => 'menu.class_list',
                            'route' => 'academics.classes.index',
                            'permission' => 'academics.view',
                            'active' => ['academics.classes.*', 'academics.streams.*'],
                        ],
                        [
                            'label' => 'menu.subjects',
                            'route' => 'academics.subjects.index',
                            'permission' => 'academics.view',
                            'active' => ['academics.subjects.*'],
                        ],
                    ],
                ],
            ],
        ],
        [
            'label' => 'menu.sections.finance',
            'items' => [
                [
                    'label' => 'menu.fees',
                    'icon' => 'ti ti-cash',
                    'children' => [
                        [
                            'label' => 'menu.invoices',
                            'route' => 'fees.invoices.index',
                            'permission' => 'fees.view',
                            'active' => ['fees.invoices.*'],
                        ],
                        [
                            'label' => 'menu.payments',
                            'route' => 'fees.payments.index',
                            'permission' => 'fees.view',
                            'active' => ['fees.payments.*', 'fees.receipts.*'],
                        ],
                        [
                            'label' => 'menu.fee_reminders',
                            'route' => 'communication.reminders.index',
                            'permission' => 'reminders.send',
                            'active' => ['communication.reminders.*'],
                        ],
                        [
                            'label' => 'menu.collections_report',
                            'route' => 'fees.reports.collections',
                            'permission' => 'fees.view',
                            'active' => ['fees.reports.*'],
                        ],
                        [
                            'label' => 'menu.fee_structures',
                            'route' => 'fees.structures.index',
                            'permission' => 'fees.view',
                            'active' => ['fees.structures.*'],
                        ],
                    ],
                ],
            ],
        ],
        [
            'label' => 'menu.sections.settings',
            'items' => [
                [
                    'label' => 'menu.settings',
                    'icon' => 'ti ti-settings',
                    'children' => [
                        [
                            'label' => 'menu.integrations',
                            'route' => 'settings.integrations.index',
                            'permission' => 'settings.manage',
                            'active' => ['settings.integrations.*'],
                        ],
                        [
                            'label' => 'menu.school_profile',
                            'route' => 'settings.school.index',
                            'permission' => 'settings.manage',
                            'active' => ['settings.school.*'],
                        ],
                        [
                            'label' => 'menu.admission_numbers',
                            'route' => 'settings.admission.index',
                            'permission' => 'settings.manage',
                            'active' => ['settings.admission.*'],
                        ],
                        [
                            'label' => 'menu.users',
                            'route' => 'settings.users.index',
                            'permission' => 'settings.manage',
                            'active' => ['settings.users.*'],
                        ],
                        [
                            'label' => 'menu.roles',
                            'route' => 'settings.roles.index',
                            'permission' => 'settings.manage',
                            'feature' => 'custom_roles',
                            'active' => ['settings.roles.*'],
                        ],
                    ],
                ],
            ],
        ],
        [
            'label' => 'menu.sections.family',
            'items' => [
                [
                    'label' => 'menu.my_children',
                    'icon' => 'ti ti-users-group',
                    'route' => 'family.index',
                    'permission' => 'family.portal',
                    'active' => ['family.*'],
                ],
            ],
        ],
        [
            'label' => 'menu.sections.platform',
            'items' => [
                [
                    'label' => 'menu.schools',
                    'icon' => 'ti ti-building-community',
                    'route' => 'platform.schools.index',
                    'scope' => 'central',
                    'active' => ['platform.schools.*'],
                ],
            ],
        ],
        [
            'label' => 'menu.sections.support',
            'items' => [
                [
                    'label' => 'menu.help',
                    'icon' => 'ti ti-help-circle',
                    'route' => 'help.index',
                    'scope' => 'both',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Quick actions
    |--------------------------------------------------------------------------
    |
    | The "Add new" menu of the top bar: the pages a person reaches for most
    | often. An entry is shown only when the route exists and the person holds
    | the permission that guards it, exactly like the entries of the sidebar.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Settings pages
    |--------------------------------------------------------------------------
    |
    | The pages of the school's settings, in the order the "Settings" link of
    | the top bar tries them. The first one that exists and that the person
    | may open is the one the link leads to.
    |
    */

    'settings_routes' => ['settings.integrations.index', 'settings.admission.index', 'settings.school.index'],

    'quick' => [[
        'label' => 'menu.quick_add_student',
        'icon' => 'ti ti-user-plus',
        'route' => 'students.create',
        'permission' => 'students.manage',
    ],
        [
            'label' => 'menu.quick_import_students',
            'icon' => 'ti ti-upload',
            'route' => 'students.import',
            'permission' => 'students.manage',
        ],
        [
            'label' => 'menu.quick_attendance',
            'icon' => 'ti ti-calendar-check',
            'route' => 'attendance.index',
            'permission' => 'attendance.manage',
        ],
        [
            'label' => 'menu.quick_exam',
            'icon' => 'ti ti-file-certificate',
            'route' => 'exams.create',
            'permission' => 'exams.manage',
        ],
        [
            'label' => 'menu.quick_grading_scale',
            'icon' => 'ti ti-chart-bar',
            'route' => 'grading-scales.create',
            'permission' => 'grades.manage',
        ],
        [
            'label' => 'menu.quick_fee_structure',
            'icon' => 'ti ti-cash',
            'route' => 'fees.structures.create',
            'permission' => 'fees.manage',
        ],
        [
            'label' => 'menu.quick_payment',
            'icon' => 'ti ti-receipt',
            'route' => 'fees.payments.create',
            'permission' => 'payments.record',
        ],
    ],

];
