<?php

namespace App\Filament\Pages;

use App\Imports\ProductImporter;
use App\Models\Import;
use Filament\Actions\ImportAction;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use JibayMcs\FilamentTour\Tour\HasTour;
use JibayMcs\FilamentTour\Tour\Step;
use JibayMcs\FilamentTour\Tour\Tour;

class ImportProducts extends Page implements HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable, HasTour;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationGroup = 'Магазин';
    
    protected static ?string $navigationLabel = 'Импорт товаров';

    protected static ?string $title = 'Импорт товаров';

    protected static string $view = 'filament.pages.import-products';

    public function tours(): array {
        return [
        //    Tour::make('dashboard')
        //        ->steps(
     
        //            Step::make()
        //                ->title("Welcome to your Dashboard !")
        //                ->description('You look nice !'),
     
        //            Step::make('.fi-avatar')
        //                ->title('Woaw ! Here is your avatar !')
        //                ->description('You look nice !')
        //                ->icon('heroicon-o-user-circle')
        //                ->iconColor('primary')
        //        ),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Import::query()->where('importer', ProductImporter::class))
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Дата импорта')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn ($record) => $record ? match ($record->status) {
                        'completed' => 'success',
                        'processing' => 'warning',
                        'failed' => 'danger',
                        default => 'secondary',
                    } : 'secondary')
                    ->formatStateUsing(fn ($record) => $record ? match ($record->status) {
                        'pending' => 'Ожидает',
                        'processing' => 'Обработка',
                        'completed' => 'Завершен',
                        'failed' => 'Ошибка',
                        default => $record->status,
                    } : ''),

                TextColumn::make('error')
                    ->label('Ошибка')
                    ->visible(fn ($record) => $record && $record->status === 'failed'),

                TextColumn::make('total_rows')
                    ->label('Всего записей')
                    ->sortable(),

                TextColumn::make('successful_rows')
                    ->label('Успешно')
                    ->sortable(),

                TextColumn::make('failed_rows')
                    ->label('Ошибки')
                    ->sortable(),

                TextColumn::make('processed_rows')
                    ->label('Обновлено')
                    ->state(function (Import $record): string {
                        return $record->processed_rows - ($record->created_rows ?? 0);
                    }),

                TextColumn::make('created_rows')
                    ->label('Создано')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('1s')
            ->paginated([10, 25, 50]);
    }

    public function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(ProductImporter::class)
                ->chunkSize(1),
        ];
    }
}