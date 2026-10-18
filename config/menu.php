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
    ],

];
