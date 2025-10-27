<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = "ri-article-line";

    protected static ?string $navigationGroup = "Блог";
    protected static ?string $navigationLabel = "Новости";
    protected static ?string $modelLabel = "Новость";
    protected static ?string $pluralModelLabel = "Новости";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Split::make([
                Section::make([
                    Forms\Components\TextInput::make("title")
                        ->label("Название")
                        ->required(),
                    Forms\Components\Textarea::make("short_content")
                        ->label("Анонс")
                        ->required(),
                    PageBuilder::make("content")
                        ->required()
                        ->label(
                            __(
                                "filament-fabricator::page-resource.labels.blocks"
                            )
                        ),
                ]),
                Section::make([
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label("Дата публикации")
                        ->seconds(false),
                    Forms\Components\Toggle::make("is_popular")
                        ->label("Популярная")
                        ->required(),
                    Forms\Components\TextInput::make("slug")
                        ->label("Ссылка")
                        ->required(),
                    Forms\Components\FileUpload::make("image")
                        ->required()
                        ->image()
                        ->label("Картинка")
                        ->directory("Articles")
                        ->visibility("public")
                        ->imageEditor()
                        ->preserveFilenames()
                        ->imageEditorMode(2),
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
                Tables\Columns\TextColumn::make("title")
                    ->label("Название")
                    ->searchable(),
                Tables\Columns\ImageColumn::make("image")->label("Изображение"),
                Tables\Columns\IconColumn::make("is_popular")
                    ->label("Популярная")
                    ->boolean(),
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
            ->filters([
                //
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListPosts::route("/"),
            "create" => Pages\CreatePost::route("/create"),
            "edit" => Pages\EditPost::route("/{record}/edit"),
        ];
    }
}
