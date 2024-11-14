<?php

declare(strict_types=1);

return [
    'form' => [
        'title' => 'Заголовок',
        'url' => 'URL',
        'linkable_type' => 'Тип',
        'linkable_id' => 'ID',
    ],
    'resource' => [
        'name' => [
            'label' => 'Название',
        ],
        'locations' => [
            'label' => 'Расположение',
            'empty' => 'Не задано',
        ],
        'items' => [
            'label' => 'Элементы',
        ],
        'is_visible' => [
            'label' => 'Видимость',
            'visible' => 'Показано',
            'hidden' => 'Скрыто',
        ],
    ],
    'actions' => [
        'add' => [
            'label' => 'Добавить',
        ],
        'locations' => [
            'label' => 'Связи',
            'heading' => 'Отредактируйте связи',
            'description' => 'Привяжите меню к соответствующим блокам.',
            'submit' => 'Обновить',
            'form' => [
                'location' => [
                    'label' => 'Расположение',
                ],
                'menu' => [
                    'label' => 'Меню',
                ],
            ],
            'empty' => [
                'heading' => 'Тут ничего нет. Зовите разработчика.',
            ],
        ],
    ],
    'items' => [
        'expand' => 'Развернуть',
        'collapse' => 'Свернуть',
        'empty' => [
            'heading' => 'Тут ничего нет.',
        ],
    ],
    'custom_link' => 'Кастомная ссылка',
    'custom_text' => 'Кастомный текст',
    'open_in' => [
        'label' => 'Открыть в',
        'options' => [
            'self' => 'Этой вкладке',
            'blank' => 'Новой вкладке',
            'parent' => 'Родительской вкладке',
            'top' => 'Вкладке выше',
        ],
    ],
    'notifications' => [
        'created' => [
            'title' => 'Ссылка создана',
        ],
        'locations' => [
            'title' => 'Расположения меню обновлены',
        ],
    ],
    'panel' => [
        'empty' => [
            'heading' => 'Ничего не найдено',
            'description' => 'В этом меню ничего нет.',
        ],
        'pagination' => [
            'previous' => 'Пред.',
            'next' => 'Далее',
        ],
    ],
];
