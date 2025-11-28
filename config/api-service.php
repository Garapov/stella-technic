<?php

return [
    'navigation' => [
        'token' => [
            'cluster' => null,
            'group' => 'Настройки сайта',
            'sort' => -1,
            'icon' => 'heroicon-o-key',
            'should_register_navigation' => true,
        ],
    ],
    'models' => [
        'token' => [
            'enable_policy' => false,
        ],
    ],
    'route' => [
        'panel_prefix' => true,
        'use_resource_middlewares' => false,
    ],
    'tenancy' => [
        'enabled' => false,
        'awareness' => false,
    ],
    'login-rules' => [
        'email' => 'required|email',
        'password' => 'required',
    ],
    'use-spatie-permission-middleware' => true,
];
