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

    protected static ?int $navigationSort = 3;

    public function tours(): array {
        return [
        //    Tour::make('dashboard')
        //        ->steps(
     
        //            Step::make()
        //                ->title("Добро поожалоовать на страницу импорта товаров !")
        //                ->description('Давайте научимся !'),
     
        //            Step::make('.fi-ac-btn-action')
        //                ->title('Нажмите кнопку')
        //                ->icon('heroicon-o-user-circle')
        //                ->iconColor('primary'),

        //             Step::make('.filepond--drop-label')
        //                ->title('Загрузите файл csv')
        //                ->icon('heroicon-o-user-circle')
        //                ->iconColor('primary')
        //        ),
        ];
    }

    public function getActions(): array
    {
        return [
            ImportAction::make()
                ->importer(ProductImporter::class)
                ->chunkSize(100)
                ->color('primary')
                ->maxRows(1000)
                ->label('Импортировать товары')
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Import::query()->where('importer', ProductImporter::class))
            ->defaultSort('created_at', 'desc')
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

                // TextColumn::make('file_name')
                //     ->label('Файл')
                //     ->searchable(),

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
                    ->visible(fn ($record) => $record && $record->status === 'failed')
                    ->wrap(),

                TextColumn::make('created_rows')
                    ->label('Создано')
                    ->sortable(),

                TextColumn::make('updated_rows')
                    ->label('Обновлено')
                    ->sortable(),

                TextColumn::make('failed_rows')
                    ->label('Ошибки')
                    ->sortable()
                    ->color('danger'),

                TextColumn::make('processed_at')
                    ->label('Обработано')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])->filters([
                // Add filters if needed
            ])->actions([
                // Add actions if needed
            ])->bulkActions([
                // Add bulk actions if needed
            ])->poll('10s')->striped();
    }
}