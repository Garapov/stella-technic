<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Filament\Resources\ProductCategoryResource\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\ProductCategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use App\Models\ProductParamItem;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = "carbon-center-circle";
    protected static ?string $navigationLabel = "Категории товаров";
    protected static ?string $modelLabel = "Категорию товаров";
    protected static ?string $pluralModelLabel = "Категории товаров";
    protected static ?string $navigationGroup = "Магазин";
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = "title";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Split::make([
                Tabs::make("Tabs")->tabs([
                    Tabs\Tab::make("Основная информация")->schema([
                        TextInput::make("title")
                            ->label("Заголовок")
                            ->required(),
                        TextInput::make("slug")->label("Имя в ссылке"),
                        Textarea::make("description")
                            ->label("Описание")
                            ->required(),
                        Section::make('Параметры фильтрации')
                            ->description(fn (Get $get) => $get("params_to_one") ? 'Все параметры учитываются у одного товара' : 'Любой из параметров учитывается у одного товара')
                            ->schema([
                                Select::make("paramItems")
                                    ->label(false)
                                    ->multiple()
                                    ->relationship("paramItems", "title")
                                    ->preload()
                                    ->options(function () {
                                        return ProductParamItem::query()
                                            ->with("productParam")
                                            ->get()
                                            ->mapWithKeys(function ($item) {
                                                return [
                                                    $item->id => "{$item->productParam->name}: {$item->title}",
                                                ];
                                            });
                                    }),
                                    Toggle::make('params_to_one')
                                        ->label(fn (Get $get) => $get("params_to_one") ? 'Только выбранные' : 'Любой из параметров')
                                        ->live()
                                        ->onColor('success')
                                        ->offColor('danger')
                            ])->visible(fn(Get $get) => $get("type") == "filter"),
                        Select::make("variations")
                            ->label("Вариации")
                            ->multiple()
                            ->relationship("variations", "name")
                            ->preload()
                            ->options(function () {
                                return ProductVariant::query()
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => "{$item->name} ({$item->sku})",
                                        ];
                                    });
                            })
                            ->visible(
                                fn(Get $get) => $get("type") == "variations",
                            ),
                        Select::make("products")
                                ->label('Родительские товары вариаций')
                                ->multiple()
                                ->relationship("products", "name")
                                ->preload()
                                ->options(function () {
                                    return Product::query()
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            return [
                                                $item->id => "{$item->name}",
                                            ];
                                        });
                                })
                                ->visible(fn(Get $get) => $get('type') == null),

                        Select::make("duplicate_id")
                            ->label("Категория")
                            ->preload()
                            ->searchable()
                            ->multiple()
                            ->options(function () {
                                return ProductCategory::query()
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => "{$item->title}",
                                        ];
                                    });
                            })
                            ->visible(
                                fn(Get $get) => $get("type") == "duplicator" ||
                                    $get("type") == "filter",
                            ),
                    ]),
                    Tabs\Tab::make("Изображения")->schema([
                        FileUpload::make("image")
                            ->directory("categories")
                            ->label("Картинка")
                            ->required()
                            ->image(),
                        Hidden::make("icon")->default("fas-box-archive"),
                        // IconPicker::make("icon")->required(),
                    ]),
                    Tabs\Tab::make("SEO")->schema([
                        Builder::make("seo")
                            ->label("SEO данные")
                            ->addActionLabel("Добавить данные")
                            ->blockNumbers(false)
                            ->blocks([
                                Builder\Block::make("title")
                                    ->label("Заголовок")
                                    ->schema([
                                        TextInput::make("title")
                                            ->label("Заголовок")
                                            ->required(),
                                    ])
                                    ->maxItems(1),
                                Builder\Block::make("description")
                                    ->label("Описание")
                                    ->schema([
                                        Textarea::make("description")
                                            ->label("Описание")
                                            ->required(),
                                    ])
                                    ->maxItems(1),
                                Builder\Block::make("image")
                                    ->label("Картинка")
                                    ->schema([
                                        FileUpload::make("image")
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
                            ->columns(2)
                            ->defaultItems(0),
                    ]),
                ]),
                Section::make([
                    Select::make("type")
                        ->label("Тип категории")
                        ->options([
                            "variations" => "Избранные вариации",
                            "filter" => "Категория-фильтр",
                            "duplicator" => "Дубликат категории",
                        ])
                        ->live(),
                    Toggle::make("is_visible")
                        ->inline(false)
                        ->default(true)
                        ->label("Видимость"),
                    Toggle::make("tabs_categories")
                        ->inline(false)
                        ->default(true)
                        ->label("Отображать подкатегории табами"),
                ])->grow(false),
            ])
                ->columnSpanFull()
                ->from("md"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")->label("Картинка"),
                Tables\Columns\TextColumn::make("id")->label("ID"),
                Tables\Columns\TextColumn::make("title")
                    ->label("Название")
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('sort')->label("Сортировка"),
                Tables\Columns\TextColumn::make("type")->label("Тип категории"),
                Tables\Columns\ToggleColumn::make("is_tag")->label(
                    'Категория "тег"',
                ),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make("Перенести")
                        ->icon("carbon-port-definition")
                        ->form([
                            Select::make("parent_id")
                                ->label("Новый родитель для категории")
                                ->options(
                                    fn() => ProductCategory::all()->pluck(
                                        "title",
                                        "id",
                                    ),
                                )
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (array $data, $record) {
                            $category = ProductCategory::where(
                                "id",
                                $data["parent_id"],
                            )->first();

                            if ($category) {
                                $record->parent_id = $category->id;
                                $record->save();
                            }

                            redirect(request()->header("Referer"));
                            // }
                        }),
                    Tables\Actions\Action::make("Открыть")
                        ->icon("ionicon-open-outline")
                        ->url(
                            fn(ProductCategory $record): string => route(
                                "client.catalog",
                                $record->urlChain(),
                            ),
                        )
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [CategoriesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            // "tree-list" => Pages\ProductCategoryTree::route("/tree-list"),
            "index" => Pages\ListProductCategories::route("/"),
            "create" => Pages\CreateProductCategory::route("/create"),
            "edit" => Pages\EditProductCategory::route("/{record}/edit"),
        ];
    }
}
