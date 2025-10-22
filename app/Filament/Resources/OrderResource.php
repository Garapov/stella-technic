<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Components\Placeholder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = "heroicon-o-shopping-bag";
    protected static ?string $navigationGroup = "Магазин";
    protected static ?string $navigationLabel = "Заказы";
    protected static ?string $modelLabel = "Заказ";
    protected static ?string $pluralModelLabel = "Заказы";
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        // dd($form);
        return $form
            ->schema([
                Split::make([
                    Tabs::make("Tabs")->tabs([
                        Tabs\Tab::make("Информация о заказе")->schema([
                            Placeholder::make('info')
                                ->label('Товары из каталога')
                                ->content('Список товаров из каталога'),
                            Repeater::make("cart_items")
                                ->label(false)
                                ->schema([
                                    Forms\Components\TextInput::make("quantity")
                                        ->label("Количество")
                                        ->numeric()
                                        ->required()
                                        ->minValue(1)
                                        ->default(1)
                                        ->disabled(),
                                    Forms\Components\TextInput::make("price")
                                        ->label("Цена")
                                        ->numeric()
                                        ->required()
                                        ->prefix("₽")
                                        ->default(0),
                                ])
                                ->itemLabel(
                                    fn(array $state): ?string => $state[
                                        "name"
                                    ] ?? null
                                )
                                ->columns(2)
                                ->collapsible()
                                ->disabled()
                                ->afterStateUpdated(function (
                                    Forms\Set $set,
                                    $state
                                ) {
                                    // Calculate total price
                                    $total = collect($state)->reduce(function (
                                        $total,
                                        $item
                                    ) {
                                        return $total +
                                            floatval($item["price"] ?? 0) *
                                                intval($item["quantity"] ?? 1);
                                    }, 0);

                                    $set("total_price", $total);
                                }),
                            Textarea::make("message")
                                ->label("Примечания к заказу")
                                ->maxLength(255)
                                ->disabled(),
                        ]),
                        Tabs\Tab::make("Информация о заказчике")
                            ->schema([
                                Forms\Components\TextInput::make("user.name")
                                    ->label("Полное имя")
                                    ->maxLength(255)
                                    ->disabled(),
                                Forms\Components\TextInput::make("user.email")
                                    ->label("Email")
                                    ->maxLength(255)
                                    ->disabled(),
                                Forms\Components\TextInput::make("user.phone")
                                    ->label("Телефон")
                                    ->disabled(),
                                Forms\Components\TextInput::make(
                                    "user.company_name"
                                )
                                    ->label("Название компании")
                                    ->hidden(
                                        fn(Get $get) => $get("user.type") !==
                                            "legal"
                                    )
                                    ->maxLength(255)
                                    ->disabled(),
                                Forms\Components\TextInput::make("user.inn")
                                    ->label("ИНН")
                                    ->hidden(
                                        fn(Get $get) => $get("user.type") !==
                                            "legal"
                                    )
                                    ->disabled(),
                                Forms\Components\TextInput::make("user.bik")
                                    ->label("БИК")
                                    ->hidden(
                                        fn(Get $get) => $get("user.type") !==
                                            "legal"
                                    )
                                    ->disabled(),
                                Forms\Components\TextInput::make(
                                    "user.correspondent_account"
                                )
                                    ->label("Корреспондентский счет")
                                    ->hidden(
                                        fn(Get $get) => $get("user.type") !==
                                            "legal"
                                    )
                                    ->disabled(),
                                Forms\Components\TextInput::make(
                                    "user.bank_account"
                                )
                                    ->label("Банковский счет")
                                    ->hidden(
                                        fn(Get $get) => $get("user.type") !==
                                            "legal"
                                    )
                                    ->disabled(),
                                Forms\Components\Textarea::make(
                                    "user.yur_address"
                                )
                                    ->label("Юридический адрес")
                                    ->hidden(
                                        fn(Get $get) => $get("user.type") !==
                                            "legal"
                                    )
                                    ->columnSpanFull()
                                    ->disabled(),
                            ])
                            ->columns(2),
                        Tabs\Tab::make("Информация о доставке")
                            ->schema([
                                Forms\Components\TextInput::make(
                                    "delivery.name"
                                )
                                    ->label("Название")
                                    ->maxLength(255)
                                    ->disabled(),
                                Forms\Components\TextInput::make(
                                    "shipping_address"
                                )
                                    ->label("Адрес доставки")
                                    ->maxLength(255)
                                    ->columnSpan(2)
                                    ->disabled(),
                            ])
                            ->columns(3),
                    ]),
                    Section::make([
                        Forms\Components\TextInput::make("total_price")
                            ->label("Общая стоимость заказа")
                            ->numeric()
                            ->prefix("₽")
                            ->default(0)
                            ->disabled(),

                        Select::make("status")
                            ->label("Статус заказа")
                            ->options([
                                "pending" => "Ожидает обработки",
                                "confirmed" => "Подтвержден",
                                "shipped" => "Отправлен",
                                "delivered" => "Доставлен",
                                "cancelled" => "Отменен",
                            ])
                            ->default("pending")
                            ->native(false),
                    ])->grow(false),
                ])->from("md"),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id")->label("ID#")->sortable(),
                TextColumn::make("name")->label("Заказчик")->searchable(),
                TextColumn::make("email")->label("Email")->searchable(),
                TextColumn::make("total_price")
                    ->label("Цена")
                    ->numeric(decimalPlaces: 2)
                    ->money("RUB")
                    ->sortable(),
                TextColumn::make("status")
                    ->label("Статус")
                    ->badge()
                    ->sortable()
                    ->color(
                        fn(string $state): string => match (
                            strtolower($state)
                        ) {
                            "pending" => "warning",
                            "confirmed" => "info",
                            "shipped" => "primary",
                            "delivered" => "success",
                            "cancelled" => "danger",
                            default => "gray",
                        }
                    )
                    ->icon(
                        fn(string $state): string => match (
                            strtolower($state)
                        ) {
                            "pending" => "heroicon-o-clock",
                            "confirmed" => "heroicon-o-check",
                            "shipped" => "heroicon-o-truck",
                            "delivered" => "heroicon-o-check-badge",
                            "cancelled" => "heroicon-o-x-circle",
                            default => "heroicon-o-question-mark-circle",
                        }
                    ),
                TextColumn::make("created_at")
                    ->label("Дата")
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("status")->options([
                    "pending" => "Ожидает обработки",
                    "confirmed" => "Подтвержден",
                    "shipped" => "Отправлен",
                    "delivered" => "Доставлен",
                    "cancelled" => "Отменен",
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort("created_at", "desc");
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where("status", "pending")->get()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where("status", "pending")->get()->count() >
            10
            ? "gray"
            : "gray";
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListOrders::route("/"),
            // 'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
            "view" => Pages\ViewOrder::route("/{record}"),
        ];
    }
}
