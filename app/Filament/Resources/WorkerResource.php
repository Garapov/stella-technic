<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Filament\Resources\WorkerResource\RelationManagers;
use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static ?string $navigationIcon = "carbon-user-sponsor";
    protected static ?string $navigationLabel = "Сотрудники";
    protected static ?string $modelLabel = "Сотрудник";
    protected static ?string $pluralModelLabel = "Сотрудники";
    protected static ?string $navigationGroup = "Страницы";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Split::make([
                Tabs::make("Tabs")->tabs([
                    Tabs\Tab::make("Основная информация")->schema([
                        Forms\Components\FileUpload::make("image")
                            ->required()
                            ->image()
                            ->label("Картинка")
                            ->directory("workers")
                            ->visibility("public")
                            ->imageEditor()
                            ->preserveFilenames()
                            ->imageEditorMode(2),
                        Forms\Components\TextInput::make("name")
                            ->label("Имя")
                            ->required(),
                        Forms\Components\TextInput::make("position")
                            ->label("Должность")
                            ->required(),
                    ]),
                    Tabs\Tab::make("Описание")->schema([
                        Forms\Components\Textarea::make("description")
                            ->label("Описание")
                            ->columnSpanFull(),
                    ]),
                    Tabs\Tab::make("Контакты")->schema([
                        Forms\Components\TextInput::make("phone")
                            ->tel()
                            ->telRegex(
                                '/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'
                            )
                            ->label("Телефон"),
                        Forms\Components\TextInput::make("phone_ext")->label(
                            "Телефон (доп.)"
                        ),
                        Forms\Components\TextInput::make("email")
                            ->email()
                            ->label("Email"),
                    ]),
                ]),
                Section::make([
                    Forms\Components\Toggle::make("is_active")
                        ->required()
                        ->label("Активен"),
                ])->grow(false),
            ])
                ->from("md")
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")->label("Изображение"),
                Tables\Columns\TextColumn::make("name")
                    ->searchable()
                    ->label("Имя"),
                Tables\Columns\TextColumn::make("position")
                    ->searchable()
                    ->label("Должность"),
                Tables\Columns\IconColumn::make("is_active")
                    ->boolean()
                    ->label("Активен"),
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
            "index" => Pages\ListWorkers::route("/"),
            "create" => Pages\CreateWorker::route("/create"),
            "edit" => Pages\EditWorker::route("/{record}/edit"),
        ];
    }
}
