<?php

namespace App\Filament\Resources\ProductCategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Builder as ComponentsBuilder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';
    protected static ?string $title = "Подкатегории";

    public function form(Form $form): Form
    {
        return $form->schema([
            Split::make([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->schema([
                                TextInput::make("title")->label("Заголовок")->required(),
                                TextInput::make("slug")->label("Имя в ссылке"),
                                Textarea::make("description")->label("Описание")->required(),
                                // Select::make("paramItems")
                                //     ->multiple()
                                //     ->relationship("paramItems", "title")
                                //     ->preload()
                                //     ->options(function () {
                                //         return ProductParamItem::query()
                                //             ->with("productParam")
                                //             ->get()
                                //             ->mapWithKeys(function ($item) {
                                //                 return [
                                //                     $item->id => "{$item->productParam->name}: {$item->title}",
                                //                 ];
                                //             });
                                //     }),
                            ]),
                        Tabs\Tab::make('Изображения')
                            ->schema([
                                FileUpload::make("image")
                                    ->directory("categories")
                                    ->label("Картинка")
                                    ->required()
                                    ->image(),
                                Hidden::make('icon')->default("fas-box-archive"),
                                // IconPicker::make("icon")->required(),
                            ]),
                        Tabs\Tab::make('SEO')
                            ->schema([
                                ComponentsBuilder::make('seo')
                                    ->label('SEO данные')
                                    ->addActionLabel('Добавить данные')
                                    ->blockNumbers(false)
                                    ->blocks([
                                        ComponentsBuilder\Block::make('title')
                                            ->label("Заголовок")
                                            ->schema([
                                                TextInput::make("title")->label("Заголовок")->required(),
                                            ])->maxItems(1),
                                        ComponentsBuilder\Block::make('description')
                                            ->label("Описание")
                                            ->schema([
                                                Textarea::make("description")->label("Описание")->required(),
                                            ])->maxItems(1),
                                        ComponentsBuilder\Block::make('image')
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
                                            ])->maxItems(1)
                                    ])
                            ]),
                        Tabs\Tab::make('Файлы')
                            ->schema([
                                Repeater::make('files')
                                    ->label('Список файлов')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Название')
                                            ->required(),
                                        FileUpload::make("file")
                                            ->required()
                                            ->label("Файл")
                                            ->directory("product_files")
                                            ->visibility("public")
                                            ->preserveFilenames(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                            ])
                    ]),                    
                Section::make([
                    Toggle::make("is_visible")->inline(false)->label("Видимость"),
                ])->grow(false),
            ])->columnSpanFull()->from('md')
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")->label("Картинка"),
                Tables\Columns\TextColumn::make("title")
                    ->label("Название")
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_tag')
                    ->label('Категория "тег"'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
