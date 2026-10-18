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
        'forgot-password' => [
            'title' => 'Forgot your password',
            'summary' => 'How to choose a new password.',
            'body' => [
                'On the sign-in page, choose "Forgot password?", type the email address you use at your school and choose "Send reset link".',
                'For safety, the page gives the same answer whether or not the address belongs to your school. If it does, a link arrives by email.',
                'Open the link, choose a new password of at least 8 characters, type it twice and save. The link works for 60 minutes and only once.',
                'A link only works at the school that sent it. If you work at two schools, ask for a link at each school\'s own address.',
                'If no email arrives, check your spam folder, wait a minute and try again, or ask your school administrator.',
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
