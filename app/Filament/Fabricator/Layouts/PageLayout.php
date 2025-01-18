<?php

namespace App\Filament\Fabricator\Layouts;

use Z3d0X\FilamentFabricator\Layouts\Layout;

class PageLayout extends Layout
{
    protected static ?string $name = 'page';

    public static function getLabel(): string
    {
        return 'Пустая страница';
    }
    
}