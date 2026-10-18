<?php

return [
    'title' => 'Help',
    'intro' => 'Short guides on how to use Somaplus.',
    'back' => 'All help articles',
    'articles' => [
        'getting-started' => [
            'title' => 'Getting started',
            'summary' => 'Sign in, change the language and find your way around.',
            'body' => [
                'Open your school\'s Somaplus address in the browser and sign in with your email address and password. Each school has its own address, and you can only sign in at your own school.',
                'After five wrong passwords in a row, sign-in is blocked for about a minute. Wait and try again.',
                'The first page you see is the dashboard. It shows your school, its plan, the state of its subscription and your role.',
                'To change the language, use the flag in the top bar and choose English or Kiswahili. Menus, messages and these help articles change at once, and your choice is kept when you sign out.',
                'The menu on the left lists only what you may use. If an item you expect is missing, ask your school administrator about your role.',
                'To leave, open your profile in the top bar and choose Log out.',
            ],
        ],
        'roles' => [
            'title' => 'Roles',
            'summary' => 'The five roles a person can have in a school.',
            'body' => [
                'Every school has the same five roles: Owner, Head teacher, Teacher, Accountant and Parent / student.',
                'A role decides what a person can see and do. The menu shows only the items that the person\'s role allows.',
                'A person\'s role belongs to one school. The same person at another school needs a role there as well.',
                'Your role is shown on the dashboard and in the profile menu.',
            ],
        ],
        'subscription' => [
            'title' => 'Subscription and plan',
            'summary' => 'What Active, Grace period and Read-only mean.',
            'body' => [
                'Your school\'s plan decides which features are available. The dashboard shows the plan and the state of the subscription.',
                'Active: everything in the plan works.',
                'Grace period: the subscription has lapsed, but everything still works until the date shown on the dashboard. Ask your administrator to renew.',
                'Read-only: you can sign in and read all your information, but nothing can be changed until the subscription is renewed. A yellow notice is shown on every page.',
                'Your school\'s data is never deleted because a subscription lapsed.',
            ],
        ],
    ],
];
