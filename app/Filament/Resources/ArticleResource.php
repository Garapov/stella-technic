<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = "ri-article-line";

    protected static ?string $navigationGroup = "Блог";
    protected static ?string $navigationLabel = "Статьи";
    protected static ?string $modelLabel = "Статья";
    protected static ?string $pluralModelLabel = "Статьи";

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
                    Forms\Components\Toggle::make("is_popular")
                        ->label("Популярная")
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
            "index" => Pages\ListArticles::route("/"),
            "create" => Pages\CreateArticle::route("/create"),
            "edit" => Pages\EditArticle::route("/{record}/edit"),
        ];
    }
}
