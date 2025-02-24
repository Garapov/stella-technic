<?php

return [
    'title' => 'Глобальные настройки',
    'group' => 'Настройки сайта',
    'back' => 'Назад',
    'settings' => [
        'site' => [
            'title' => 'Настройки сайта',
            'description' => 'Управление настройками сайта',
            'form' => [
                'site_name' => 'Название сайта',
                'site_description' => 'Описание сайта',
                'site_logo' => 'Логотип сайта',
                'site_profile' => 'Профиль сайта',
                'site_keywords' => 'Ключевые слова',
                'site_email' => 'Электронная почта',
                'site_phone' => 'Телефон',
                'site_author' => 'Автор',
            ],
            'site-map' => 'Создать карту сайта',
            'site-map-notification' => 'Карта сайта успешно создана',
        ],
        'social' => [
            'title' => 'Меню социальных сетей',
            'description' => 'Управление социальными ссылками',
            'form' => [
                'site_social' => 'Ссылки на социальные сети',
                'vendor' => 'Вендор',
                'link' => 'Ссылка',
            ],
        ],
    ],
];
