<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MainSliderResource\Pages;
use App\Filament\Resources\MainSliderResource\RelationManagers;
use App\Models\MainSlider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Tables\Columns\ImageByIdColumn;

class MainSliderResource extends Resource
{
    protected static ?string $model = MainSlider::class;

    protected static ?string $navigationIcon = "heroicon-o-square-3-stack-3d";
    protected static ?string $navigationLabel = "Слайды на главной";
    protected static ?string $modelLabel = "Слайд на главной";
    protected static ?string $pluralModelLabel = "Слайды на главной";
    protected static ?string $navigationGroup = "Страницы";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make("title")
                ->label("Заголовок")
                ->required()
                ->columnSpanFull(),
            Forms\Components\FileUpload::make("image")
                ->required()
                ->image()
                ->label("Картинка")
                ->directory("banners")
                ->visibility("public")
                ->imageEditor()
                ->preserveFilenames()
                ->imageCropAspectRatio("1:1")
                ->imageEditorMode(2),
            Forms\Components\RichEditor::make("description")
                ->label("Описание")
                ->required(),
            Forms\Components\TextInput::make("button_text")
                ->label("Текст кнопки")
                ->required(),
            Forms\Components\TextInput::make("link")
                ->label("Ссылка")
                ->required(),
            Forms\Components\ColorPicker::make("background")
                ->label("Цвет фона")
                ->required(),
            Forms\Components\FileUpload::make("background_image")
                ->required()
                ->image()
                ->label("Картинка фона")
                ->directory("banners")
                ->visibility("public")
                ->imageEditor()
                ->preserveFilenames()
                ->imageCropAspectRatio("21:9")
                ->imageEditorMode(2),
            Forms\Components\Toggle::make("show_on_main")
                ->label("Отображать на главной")
                ->inline(false),
            // ->description('При наличии картинки фона, будет отображаться только картинка на всей области слайда'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")->label("Картинка"),
                Tables\Columns\TextColumn::make("title")
                    ->label("Заголовок")
                    ->searchable(),
                Tables\Columns\TextColumn::make("button_text")
                    ->label("Текст кнопки")
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make("link")
                    ->label("Ссылка")
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Columns\ToggleColumn::make("show_on_main")->label(
                    "Отображать на главной"
                ),
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
            "index" => Pages\ListMainSliders::route("/"),
            "create" => Pages\CreateMainSlider::route("/create"),
            "edit" => Pages\EditMainSlider::route("/{record}/edit"),
        ];
    }
}
