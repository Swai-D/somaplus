<?php

/*
|--------------------------------------------------------------------------
| Help centre
|--------------------------------------------------------------------------
|
| The categories of the Help centre, in display order: key => tabler icon.
| Articles are Markdown files in resources/docs/{locale}/{category}/{slug}.md
| (English and Swahili), with a small front-matter block: title, summary,
| keywords, order. A category without articles is not shown. Each category
| also needs its title and description in lang/en/help.php and lang/sw/help.php.
|
*/

return [

    'categories' => [
        'getting-started' => 'ti ti-rocket',
        'students' => 'ti ti-users',
        'teachers' => 'ti ti-user-check',
        'attendance' => 'ti ti-checklist',
        'library' => 'ti ti-books',
        'exams' => 'ti ti-file-certificate',
        'communication' => 'ti ti-message',
        'fees' => 'ti ti-cash',
        'academics' => 'ti ti-school',
        'account' => 'ti ti-user-circle',
        'plans' => 'ti ti-crown',
        'troubleshooting' => 'ti ti-lifebuoy',
    ],

];
