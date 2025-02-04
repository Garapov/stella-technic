<?php

namespace App\Filament\Resources;

use Stephenjude\FilamentBlog\Resources\AuthorResource;

class BlogAuthorResource extends AuthorResource
{
    protected static ?string $navigationGroup = 'Блог';
    protected static ?string $navigationLabel = 'Авторы';
    protected static ?string $modelLabel = 'Автор';
    protected static ?string $pluralModelLabel = 'Авторы';
}