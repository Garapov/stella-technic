<?php

namespace App\Filament\Resources\ProductCategoryResource\RelationManagers;

use App\Models\ProductParamItem;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = "products";
    protected static ?string $title = "Товары";

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make("Tabs")
                ->tabs([
                    Tab::make("Основная информация")
                        ->schema([
                            Forms\Components\TextInput::make("name")
                                ->required()
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
                            Forms\Components\Toggle::make("is_popular")
                                ->label("Популярный")
                                ->inline(false),
                        ])
                        ->columns([
                            "sm" => 1,
                            "xl" => 2,
                            "2xl" => 3,
                        ]),
                    Tab::make("Описание")
                        ->schema([
                            Forms\Components\Textarea::make("short_description")
                                ->required()
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
                        ]),
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
                        ]),
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
                    Tab::make("Параметры")
                        ->schema([
                            Forms\Components\Select::make("paramItems")
                                ->label("Параметры")
                                ->multiple()
                                ->relationship("paramItems", "title")
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\Select::make(
                                        "product_param_id"
                                    )
                                        ->relationship("productParam", "name")
                                        ->required(),
                                    Forms\Components\TextInput::make("title")
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make(
                                        "value"
                                    )->required(),
                                ])
                                ->options(function () {
                                    return ProductParamItem::query()
                                        ->with("productParam")
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            return [
                                                $item->id => "{$item->productParam->name}: {$item->title}",
                                            ];
                                        });
                                })
                                ->columnSpanFull(),
                        ])
                        ->columnSpan("full"),
                    Tab::make("Поиск")->schema([
                        Forms\Components\Textarea::make("synonims")
                            ->label("Синонимы для поиска")
                            ->columnSpanFull(),
                    ]),
                ])
                ->columnSpan("full")
                ->columns([
                    "sm" => 1,
                    "xl" => 1,
                    "2xl" => 1,
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute("name")
            ->columns([
                Tables\Columns\ImageColumn::make("gallery")
                    ->label("Галерея")
                    ->square()
                    ->size(50)
                    ->stacked(),
                Tables\Columns\TextColumn::make("name")->label("Название"),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalWidth(
                    MaxWidth::SevenExtraLarge
                ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth(
                    MaxWidth::SevenExtraLarge
                ),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(
                fn(Builder $query) => $query->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ])
            );
    }
}
