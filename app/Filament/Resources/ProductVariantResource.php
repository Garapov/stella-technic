<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\Product;
use App\Models\ProductParamItem;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Components\Builder as ComponentsBuilder;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = "Товары (Вариации)";
    protected static ?string $modelLabel = "Товар (Вариация)";
    protected static ?string $pluralModelLabel = "Товары (Вариации)";
    protected static ?string $navigationGroup = "Магазин";
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make("Tabs")
                ->tabs([
                    Tab::make("Основная информация")
                        ->schema([
                            TextInput::make("name")
                                ->required()
                                ->live()
                                ->label("Название"),
                            TextInput::make("h1")
                                ->required()
                                ->label("H1"),
                            TextInput::make("price")
                                ->required()
                                ->numeric()
                                ->prefix("₽")
                                ->label("Цена"),
                            TextInput::make("new_price")
                                ->numeric()
                                ->lt("price")
                                ->prefix("₽")
                                ->label("Цена со скидкой"),
                            TextInput::make("auth_price")
                                ->numeric()
                                ->lt("price")
                                ->prefix("₽")
                                ->label("Цена после авторизации"),
                            TextInput::make("count")
                                ->required()
                                ->numeric()
                                ->label("Остаток"),
                            TextInput::make("sku")
                                ->required()
                                ->label("Артикул"),
                            TextInput::make("uuid")
                                ->label("UID"),
                            Toggle::make("is_popular")
                                ->label("Популярный")
                                ->inline(false),
                            Toggle::make("is_constructable")
                                ->label("Конструктор")
                                ->live()
                                ->inline(false),
                            Toggle::make("is_hidden")
                                ->label("Скрыт")
                                ->live()
                                ->inline(false),
                            Toggle::make("is_pre_order")
                                ->label("По предзаказу")
                                ->inline(false),
                        ])
                        ->columns([
                            "sm" => 1,
                            "xl" => 2,
                            "2xl" => 3,
                        ])
                        ->columnSpan("full"),
                    Tab::make("Описание")
                        ->schema([
                            Forms\Components\Textarea::make("short_description")
                                ->label("Короткое описание")
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make("description")
                                ->toolbarButtons([
                                    "blockquote",
                                    "bold",
                                    "bulletList",
                                    "codeBlock",
                                    "h2",
                                    "h3",
                                    "italic",
                                    "link",
                                    "orderedList",
                                    "redo",
                                    "strike",
                                    "underline",
                                    "undo",
                                ])
                                ->label("Описание")
                                ->columnSpanFull(),
                        ])
                        ->columns([
                            "sm" => 1,
                            "xl" => 1,
                            "2xl" => 1,
                        ])
                        ->columnSpan("full"),
                    Tab::make("Поиск")
                        ->schema([
                            Forms\Components\Textarea::make("synonims")
                                ->label("Синонимы")
                                ->columnSpanFull(),
                        ])
                        ->columns([
                            "sm" => 1,
                            "xl" => 1,
                            "2xl" => 1,
                        ])
                        ->columnSpan("full"),
                    Tab::make("Галерея")
                        ->schema([
                            Forms\Components\FileUpload::make("gallery")
                                ->required()
                                ->image()
                                ->label("Изображения")
                                ->directory("variations")
                                ->visibility("public")
                                ->multiple()
                                ->reorderable()
                                ->panelLayout("grid")
                                ->imageEditor()
                                ->preserveFilenames()
                                ->imageCropAspectRatio("1:1")
                                ->imageEditorMode(2),
                            Forms\Components\FileUpload::make("videos")
                                ->label("Видео")
                                ->directory("variations")
                                ->visibility("public")
                                ->multiple()
                                ->acceptedFileTypes([
                                    'video/mp4',   // .mp4 (H.264/AAC) — работает везде, самый надёжный вариант
                                    'video/webm',  // .webm (VP8/VP9/Opus) — современный формат, хорошая поддержка
                                    'video/ogg',   // .ogv (Theora/Vorbis) — устаревающий, но поддержка в Firefox/Chrome
                                ])
                                ->reorderable()
                                ->panelLayout("grid")
                                ->reorderable()
                                ->preserveFilenames(),
                        ])
                        ->columns([
                            "sm" => 1,
                            "xl" => 1,
                            "2xl" => 1,
                        ])
                        ->columnSpan("full"),
                    Tab::make("Параметры")
                        ->schema([
                            Forms\Components\Select::make("paramItems")
                                ->label('Ключевые')
                                ->multiple()
                                ->relationship("paramItems", "title")
                                ->preload()
                                ->options(function () {
                                    return ProductParamItem::query()
                                        ->with("productParam")
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            $paramName = $item->productParam
                                                ? $item->productParam->name
                                                : "Unknown";
                                            $name = "$paramName: $item->title ($item->value)";
                                            return [$item->id => $name];
                                        });
                                })
                                ->columnSpanFull(),
                            Forms\Components\Select::make("parametrs")
                                ->label('Второстепенные')
                                ->multiple()
                                ->relationship("parametrs", "title")
                                ->preload()
                                ->options(function () {
                                    return ProductParamItem::query()
                                        ->with("productParam")
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            $paramName = $item->productParam
                                                ? $item->productParam->name
                                                : "Unknown";
                                            $name = "$paramName: $item->title ($item->value)";
                                            return [$item->id => $name];
                                        });
                                })
                                ->columnSpanFull(),
                        ])
                        ->columnSpan("full"),
                    Tab::make("Серия")->schema([
                        Forms\Components\Select::make("batch")
                            ->label("Серия")
                            ->relationship("batch", "name")
                            ->preload(),
                    ]),
                    Tab::make("SEO")->schema([
                        ComponentsBuilder::make("seo")
                            ->label("SEO данные")
                            ->addActionLabel("Добавить данные")
                            ->blockNumbers(false)
                            ->blocks([
                                ComponentsBuilder\Block::make("title")
                                    ->label("Заголовок")
                                    ->schema([
                                        TextInput::make("title")
                                            ->label("Заголовок")
                                            ->required(),
                                    ])
                                    ->maxItems(1),
                                ComponentsBuilder\Block::make("description")
                                    ->label("Описание")
                                    ->schema([
                                        Forms\Components\Textarea::make(
                                            "description"
                                        )
                                            ->label("Описание")
                                            ->required(),
                                    ])
                                    ->maxItems(1),
                                ComponentsBuilder\Block::make("image")
                                    ->label("Картинка")
                                    ->schema([
                                        Forms\Components\FileUpload::make(
                                            "image"
                                        )
                                            ->required()
                                            ->image()
                                            ->label("Картинка")
                                            ->directory("categories/seo")
                                            ->visibility("public")
                                            ->imageEditor()
                                            ->preserveFilenames()
                                            ->imageCropAspectRatio("1:1")
                                            ->imageEditorMode(2),
                                    ])
                                    ->maxItems(1),
                            ]),
                    ]),
                    Tabs\Tab::make("Файлы")->schema([
                        Toggle::make("show_category_files")
                            ->label("Показывать файлы из категорий")
                            ->inline(false),
                        Repeater::make("files")
                            ->label("Список файлов")
                            ->schema([
                                TextInput::make("name")
                                    ->label("Название")
                                    ->required(),
                                FileUpload::make("file")
                                    ->required()
                                    ->label("Файл")
                                    ->directory("product_files")
                                    ->visibility("public")
                                    ->preserveFilenames(),
                            ])
                            ->columns(2),
                    ]),
                    Tabs\Tab::make(" Перекрестные продажи")->schema([
                        Forms\Components\Select::make("upSells")
                            ->label("С этим товаром покупают")
                            ->placeholder("Выберите вариации")
                            ->multiple()
                            ->relationship("upSells", "name")
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} ({$record->sku})")
                            ->preload(),
                        Forms\Components\Select::make("crossSells")
                            ->label("Похожие товары")
                            ->placeholder("Выберите вариации")
                            ->multiple()
                            ->relationship("crossSells", "name")
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} ({$record->sku})")
                            ->preload(),                        
                    ]),
                    Tab::make("Конструктор")
                        ->schema([
                            Forms\Components\Select::make("constructor_type")
                                ->label("Тип конструктора")
                                ->live()
                                ->options([
                                    "deck" => "Стойки",
                                ])
                                ->columnSpanFull(),

                            Forms\Components\Select::make('selected_width')
                                ->label('Ширина стойки')
                                ->options([
                                    'slim' => 'Узкая (735 мм)',
                                    'wide' => 'Широкая (1150 мм)',
                                ])
                                ->visible(fn(Get $get) => $get("constructor_type") == 'deck'),

                            Forms\Components\Select::make('selected_height')
                                ->label('Высота стойки')
                                ->options([
                                    'low' => 'Низкая (1515 мм)',
                                    'high' => 'Высокая (2020 мм)',
                                ])
                                ->visible(fn(Get $get) => $get("constructor_type") == 'deck'),

                            Forms\Components\Select::make('selected_desk_type')
                                ->label('Тип стойки')
                                ->options([
                                    'Односторонняя' => 'Односторонняя',
                                    'Двусторонняя' => 'Двусторонняя',
                                ])
                                ->visible(fn(Get $get) => $get("constructor_type") == 'deck'),

                            Forms\Components\Select::make('selected_position')
                                ->label('Позиция стойки')
                                ->options([
                                    'on_floor' => 'На полу',
                                    'on_wall' => 'На стене',
                                ])
                                ->visible(fn(Get $get) => $get("constructor_type") == 'deck'),

                            Repeater::make("rows")
                                ->label("Ряды ящиков")
                                ->schema([
                                    Forms\Components\Select::make("size")
                                        ->label("Размер")
                                        ->options([
                                            "small" => "V1",
                                            "medium" => "V2",
                                            "large" => "V3",
                                        ])
                                        ->required(),
                                    Forms\Components\Select::make("color")
                                        ->label("Цвет")
                                        ->options([
                                            "red" => "Красный",
                                            "green" => "Зеленый",
                                            "blue" => "Синий",
                                            "yellow" => "Желтый",
                                            "gray" => "Серый",
                                        ])
                                        ->required(),
                                ])
                                ->visible(
                                    fn(Get $get) => $get("constructor_type") ==
                                        "deck"
                                )
                                ->columns(2)
                                ->cloneable()
                                ->reorderable(false)
                                ->addActionLabel("Добавить ряд ящиков"),
                        ])
                        ->visible(fn(Get $get) => $get("is_constructable")),
                ])
                ->columnSpan("full"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute("name")
            ->columns([
                Tables\Columns\TextColumn::make("id")
                    ->label("ID"),
                Tables\Columns\ImageColumn::make("gallery")
                    ->label("Галерея")
                    ->square()
                    ->size(100)
                    ->stacked()
                    ->limit(1)
                    ->limitedRemainingText(),
                // Tables\Columns\TextColumn::make("id")->label(" ID"),
                Tables\Columns\TextColumn::make("name")
                    ->label("Название")
                    ->wrap(),
                Tables\Columns\TextColumn::make("sku")->label("Артикул"),
                Tables\Columns\TextColumn::make("price")
                    ->money("RUB", locale: "ru")
                    ->sortable()
                    ->label("Цена"),
                Tables\Columns\ToggleColumn::make("is_popular")->label(
                    "Популярный"
                ),
                // Tables\Columns\ToggleColumn::make('is_default')
                //     ->label('По умолчанию')
                //     ->beforeStateUpdated(function ($record, $state) {
                //         if ($state && $record && $record->exists) {
                //             // Get the owning product through the relationship manager
                //             $product = $this->getOwnerRecord();

                //             // Unset other default variants for this product
                //             $product->variants()
                //                 ->where('id', '!=', $record->id)
                //                 ->update(['is_default' => false]);
                //         }
                //         return $state;
                //     })
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth("7xl")
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->after(function ($record) {
                        redirect(request()->header("Referer"));
                    }),
                Tables\Actions\ForceDeleteAction::make()->iconButton(),
                Tables\Actions\RestoreAction::make()->iconButton(),
                Tables\Actions\Action::make("Перенести")
                    ->icon("carbon-port-definition")
                    ->iconButton()
                    ->form([
                        Forms\Components\Select::make("parent_product")
                            ->label("Новый родитель для вариации")
                            ->options(
                                fn() => Product::all()->pluck("name", "id")
                            )
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data, $record) {
                        // Collect the ids of $model->paramItems
                        $paramItemIds = $record->paramItems
                            ->pluck("id")
                            ->toArray();

                        if (
                            $parent_product = Product::where(
                                "id",
                                $data["parent_product"]
                            )->first()
                        ) {
                            // Ensure links is an array
                            if (!is_array($parent_product->links)) {
                                $parent_product->links = [];
                            }

                            // Add the new link
                            $newLink = [
                                "row" => $paramItemIds,
                            ];

                            // Check if the link already exists
                            $linkExists = false;

                            // dd($parent_product->links);
                            foreach ($parent_product->links as $link) {
                                if (
                                    isset($link["row"]) &&
                                    $link["row"] === $paramItemIds
                                ) {
                                    $linkExists = true;
                                    break;
                                }
                            }

                            if (!$linkExists) {
                                $links = $parent_product->links;
                                $links[] = $newLink;

                                $parent_product->links = $links;
                                // dd($parent_product->links);
                                $parent_product->save();
                            }
                        }

                        // Find the $record->product->links object with a 'row' array of ids equal to the ids of $record->paramItems
                        // if ($product = $record->product) {
                        // Ensure links is an array
                        if (!is_array($record->product->links)) {
                            $record->product->links = [];
                        }

                        // Remove the 'row' that matches the paramItemIds
                        $record->product->links = array_filter(
                            $record->product->links,
                            function ($link) use ($paramItemIds) {
                                return !isset($link["row"]) ||
                                    array_intersect(
                                        $paramItemIds,
                                        $link["row"]
                                    ) !== $paramItemIds;
                            }
                        );

                        // Save the remaining data
                        $record->product->save();

                        $record->product_id = $data["parent_product"];
                        $record->save();

                        redirect(request()->header("Referer"));
                        // }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    // ...
                ]),
            ]);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        // dd($record->categories->pluck('title')->implode(', '));
        return [
            "Название" => $record->name . " ($record->sku)",
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ["name", "slug", "short_description", "description"];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
