<?php

namespace App\Filament\Pages;

use App\Imports\ProductImporter;
use Filament\Pages\Page;
use Filament\Actions\ImportAction;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

class ImportProducts extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Импорт товаров';
    protected static ?string $title = 'Импорт товаров';
    protected static string $view = 'filament.pages.import-products';

    public function getActions(): array
    {
        return [
            ImportAction::make()
                ->importer(ProductImporter::class)
                ->chunkSize(100)
                ->slideOver()
        ];
    }
} 