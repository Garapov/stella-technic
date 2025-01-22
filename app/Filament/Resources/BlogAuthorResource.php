<?php

namespace App\Filament\Resources;

use Stephenjude\FilamentBlog\Resources\AuthorResource;

class BlogAuthorResource extends AuthorResource
{
    protected static ?string $navigationGroup = 'Блог';
    protected static ?string $navigationLabel = 'Заказы';
    protected static ?string $modelLabel = 'Заказ';
    protected static ?string $pluralModelLabel = 'Заказы';
}