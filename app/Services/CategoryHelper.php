<?php

namespace App\Services;

class CategoryHelper
{
    public static function buildOptions($categories, int $depth = 0): array
    {
        $options = [];
        $count = $categories->count();

        foreach ($categories as $i => $category) {
            // Префикс для отступов
            $prefix = str_repeat('│   ', $depth);

            // └── для последнего элемента, ├── для промежуточных
            $branch = ($i + 1 === $count) ? '└── ' : '├── ';

            $options[$category->id] = $prefix . $branch . $category->title;

            if ($category->categories->isNotEmpty()) {
                $options += self::buildOptions($category->categories, $depth + 1);
            }
        }

        return $options;
    }
}
