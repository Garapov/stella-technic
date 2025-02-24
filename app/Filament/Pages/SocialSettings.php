<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Guava\FilamentIconPicker\Forms\IconPicker;
use TomatoPHP\FilamentSettingsHub\Pages\SocialMenuSettings;

class SocialSettings extends SocialMenuSettings
{
    protected function getFormSchema(): array
    {
        return [
            Grid::make(['default' => 1])->schema([
                Repeater::make('site_social')
                    ->required()
                    ->minItems(1)
                    ->label(trans('filament-settings-hub::messages.settings.social.form.site_social'))
                    ->schema([
                        IconPicker::make('icon')->label('Иконка')->required(),
                        TextInput::make('link')->url()->label(trans('filament-settings-hub::messages.settings.social.form.link'))->required(),
                    ])
                    ->hint(config('filament-settings-hub.show_hint') ? 'setting("site_social")' : null),
            ]),

        ];
    }
}
