<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailingSend;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'carbon-certificate';
    protected static ?string $navigationLabel = 'Рассылки';
    protected static ?string $modelLabel = 'Рассылка';
    protected static ?string $pluralModelLabel = 'Рассылки';
    protected static ?string $navigationGroup = 'Настройки сайта';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Forms\Components\Toggle::make('confirmation')
                    ->label('Согласие на обработку данных')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('confirmation')
                    ->label('Согласие на обработку данных')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата подписки')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Дата обновления подписки')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('updateAuthor')
                    ->label('Отправить письмо')
                    ->form([
                        MarkdownEditor::make('content')
                            ->label('Контент письма')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $subscribers = Subscription::all();
                        $recipients = $subscribers ? $subscribers->pluck('email')->toArray() : [];

                        Mail::to(env('MAIL_ADMIN_ADDRESS', 'ruslangarapov@yandex.ru'))
                        ->cc($recipients)->queue((new MailingSend($data))->onQueue('mails'));
                    })
                    ->slideOver(),
            ])
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

    public function getActions(): array
    {
        return [
            ImportAction::make()
                ->importer(ProductImporter::class)
                ->chunkSize(10)
                ->color('primary')
                ->maxRows(2000)
                ->label('Импортировать товары')
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
