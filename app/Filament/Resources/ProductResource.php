<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Forms\Components\ImagePicker;
use App\Models\Product;
use App\Models\Image;
use App\Models\ProductParam;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\ProductParamItem;
use Illuminate\Database\Eloquent\Model;
use App\Tables\Columns\ImageByIdColumn;
use Exception;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Log;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = "carbon-pull-request";
    protected static ?string $navigationLabel = "Товары";
    protected static ?string $modelLabel = "Товар";
    protected static ?string $pluralModelLabel = "Товары";
    protected static ?string $navigationGroup = "Магазин";

    protected static ?string $recordTitleAttribute = "name";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make("Tabs")
                ->tabs([
                    Tab::make("Основная информация")
                        ->schema([
                            Forms\Components\TextInput::make("name")
                                ->required()
                                ->live()
                                ->label("Название"),
                            Forms\Components\TextInput::make("price")
                                ->required()
                                ->numeric()
                                ->prefix("р.")
                                ->label("Цена"),
                            Forms\Components\TextInput::make("new_price")
                                ->numeric()
                                ->lt("price")
                                ->prefix("р.")
                                ->label("Цена со скидкой"),
                            Forms\Components\TextInput::make("count")
                                ->required()
                                ->numeric()
                                ->label("Остаток"),
                            Forms\Components\TextInput::make("sku")->label(
                                "Артикул"
                            ),
                            Forms\Components\Toggle::make("is_popular")
                                ->label("Популярный")
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
                                ->required()
                                ->label("Описание")
                                ->columnSpanFull(),
                        ])
                        ->columns([
                            "sm" => 1,
                            "xl" => 1,
                            "2xl" => 1,
                        ])
                        ->columnSpan("full"),
                    Tab::make("Изображения")
                        ->schema([
                            Forms\Components\FileUpload::make("gallery")
                                ->required()
                                ->image()
                                ->label("Галерея")
                                ->directory("products")
                                ->visibility("public")
                                ->multiple()
                                ->reorderable()
                                ->panelLayout("grid")
                                ->imageEditor()
                                ->preserveFilenames()
                                ->imageCropAspectRatio("1:1")
                                ->imageEditorMode(2),
                        ])
                        ->columns([
                            "sm" => 1,
                            "xl" => 1,
                            "2xl" => 1,
                        ])
                        ->columnSpan("full"),
                    Tab::make("Категории")
                        ->schema([
                            Forms\Components\Select::make("categories")
                                ->label("Категории")
                                ->placeholder("Выберите категории")
                                ->multiple()
                                ->relationship("categories", "title")
                                ->preload(),
                        ])
                        ->columnSpan("full"),
                    // Tab::make("Параметры")
                    //     ->schema([
                    //         Forms\Components\Select::make("paramItems")
                    //             ->multiple()
                    //             ->relationship("paramItems", "title")
                    //             ->preload()
                    //             ->createOptionForm([
                    //                 Forms\Components\Select::make(
                    //                     "product_param_id"
                    //                 )
                    //                     ->relationship("productParam", "name")
                    //                     ->required(),
                    //                 Forms\Components\TextInput::make("title")
                    //                     ->required()
                    //                     ->maxLength(255),
                    //                 Forms\Components\TextInput::make(
                    //                     "value"
                    //                 )->required(),
                    //             ])
                    //             ->options(function () {
                    //                 return ProductParamItem::query()
                    //                     ->with("productParam")
                    //                     ->get()
                    //                     ->mapWithKeys(function ($item) {
                    //                         $paramName = $item->productParam
                    //                             ? $item->productParam->name
                    //                             : "Unknown";
                    //                         $name = "$paramName: $item->title";
                    //                         return [$item->id => $name];
                    //                     });
                    //             })
                    //             ->columnSpanFull(),
                    //     ])
                    //     ->columnSpan("full"),
                    Tab::make("Вариации")
                        ->schema([
                            Repeater::make("links")
                                ->label(false)
                                ->addActionLabel("Добавить вариацию")
                                ->defaultItems(1)
                                ->collapsible()
                                ->collapsed()
                                ->reorderable(false)
                                ->itemLabel(function (
                                    array $state,
                                    Get $get
                                ): string {
                                    $title = "";

                                    $title .= $get("name");

                                    foreach ($state["row"] as $param) {
                                        if (!$param["parametrs"]) {
                                            continue;
                                        }
                                        $parametr = ProductParamItem::where(
                                            "id",
                                            $param["parametrs"]
                                        )->first();

                                        if (!$parametr) {
                                            continue;
                                        }

                                        $title .= " {$parametr->title}";
                                    }

                                    return $title;
                                })
                                ->schema([
                                    Repeater::make("row")
                                        ->label(false)
                                        ->reorderable(false)
                                        ->addActionLabel("Добавить связь")
                                        ->grid(3)
                                        ->defaultItems(3)
                                        ->minItems(2)
                                        ->maxItems(6)
                                        ->simple(
                                            Forms\Components\Select::make(
                                                "parametrs"
                                            )
                                                ->label("Параметр")
                                                ->distinct()
                                                ->required()
                                                ->live()
                                                ->searchable()
                                                ->native(false)
                                                ->options(function () {
                                                    return ProductParamItem::query()
                                                        ->with("productParam")
                                                        ->get()
                                                        ->mapWithKeys(function (
                                                            $item
                                                        ) {
                                                            $paramName = $item->productParam
                                                                ? $item
                                                                    ->productParam
                                                                    ->name
                                                                : "Unknown";
                                                            $name = "$paramName: $item->title";
                                                            return [
                                                                $item->id => $name,
                                                            ];
                                                        });
                                                })
                                        ),
                                ]),
                        ])
                        ->columnSpan("full"),
                    Tab::make("Бренд")->schema([
                        Forms\Components\Select::make("brand")
                            ->label("Бренд")
                            ->relationship("brand", "name")
                            ->preload(),
                    ]),
                ])
                ->columnSpan("full"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("gallery")
                    ->label("Галерея")
                    ->square()
                    ->size(50)
                    ->stacked(),
                Tables\Columns\TextColumn::make("name")
                    ->label("Название")
                    ->searchable(),
                Tables\Columns\TextColumn::make("price")
                    ->label("Цена")
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make("new_price")
                    ->label("Цена со скидкой")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make("count")
                    ->label("Количество")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make("created_at")
                    ->label("Дата создания")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make("updated_at")
                    ->label("Дата обновления")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        // dd($record->categories->pluck('title')->implode(', '));
        return [
            "Категории" => $record->categories->pluck("title")->implode(", "),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ["name", "slug", "short_description", "description"];
    }

    public static function getRelations(): array
    {
        return [RelationManagers\VariantsRelationManager::class];
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
            "index" => Pages\ListProducts::route("/"),
            "create" => Pages\CreateProduct::route("/create"),
            "edit" => Pages\EditProduct::route("/{record}/edit"),
        ];
    }
}
