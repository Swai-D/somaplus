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
                    'route' => 'dashboard',
                    'scope' => 'both',
                ],
            ],
        ],
        [
            'label' => 'menu.sections.school',
            'items' => [
                [
                    'label' => 'menu.academics',
                    'icon' => 'ti ti-school',
                    'children' => [
                        [
                            'label' => 'menu.academic_years',
                            'route' => 'academics.years.index',
                            'permission' => 'academics.view',
                            'active' => ['academics.years.*', 'academics.terms.*'],
                        ],
                        [
                            'label' => 'menu.classes',
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

];
