<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Магазин';
    protected static ?string $navigationLabel = 'Заказы';
    protected static ?string $modelLabel = 'Заказ';
    protected static ?string $pluralModelLabel = 'Заказы';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Customer Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->maxLength(20),
                    ])->columns(3),

                Section::make('Order Details')
                    ->schema([
                        Repeater::make('cart_items')
                            ->label('Order Items')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Product Name')
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->default(1),
                                Forms\Components\TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->required()
                                    ->prefix('₽')
                                    ->default(0)
                                    ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                        // Recalculate total when price changes
                                        $items = $get('cart_items');
                                        $total = collect($items)
                                            ->map(function ($item) use ($state, $get) {
                                                $price = $item['name'] === $get('name') ? $state : ($item['price'] ?? 0);
                                                $quantity = $item['quantity'] ?? 1;
                                                return $price * $quantity;
                                            })
                                            ->sum();
                                        
                                        $set('total_price', $total);
                                    }),
                                Forms\Components\Hidden::make('new_price')
                                    ->default(null),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                // Calculate total price
                                $total = collect($state)
                                    ->reduce(function ($total, $item) {
                                        return $total + 
                                            (floatval($item['price'] ?? 0) * 
                                             intval($item['quantity'] ?? 1));
                                    }, 0);
                                
                                $set('total_price', $total);
                            }),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Order Value')
                            ->numeric()
                            ->prefix('₽')
                            ->default(0)
                            ->disabled(),

                        Select::make('status')
                            ->label('Order Status')
                            ->options([
                                'pending' => 'Ожидает обработки',
                                'confirmed' => 'Подтвержден',
                                'shipped' => 'Отправлен',
                                'delivered' => 'Доставлен',
                                'cancelled' => 'Отменен'
                            ])
                            ->default('pending')
                            ->native(false),

                    ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Заказчик')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('total_price')
                    ->label('Цена')
                    ->numeric(decimalPlaces: 2)
                    ->money('RUB')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match (strtolower($state)) {
                        'pending' => 'heroicon-o-clock',
                        'confirmed' => 'heroicon-o-check',
                        'shipped' => 'heroicon-o-truck',
                        'delivered' => 'heroicon-o-check-badge',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime()
                    ->sortable()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Ожидает обработки',
                        'confirmed' => 'Подтвержден',
                        'shipped' => 'Отправлен',
                        'delivered' => 'Доставлен',
                        'cancelled' => 'Отменен'
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->get()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->get()->count() > 10 ? 'gray' : 'gray';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
