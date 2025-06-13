<?php

namespace App\Filament\Pages;

use App\Models\Former;
use App\Settings\ConstructorSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use TomatoPHP\FilamentSettingsHub\Pages\SiteSettings;
use Filament\Pages\Actions\Action;
use App\Models\ProductVariant;
use App\Settings\FormsSettings as SettingsFormsSettings;
use Filament\Pages\Page;

class FormsSettings extends SiteSettings
{
    protected static string $settings = SettingsFormsSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Tabs::make("Tabs")
                ->tabs([
                    Tabs\Tab::make("Формы")->schema([
                        Select::make("callback")
                            ->label("Заказать звонок")
                            ->options(
                                fn() => Former::all()->pluck(
                                    "name",
                                    "id"
                                )
                            )
                            ->searchable(),
                        Select::make("map")
                            ->label("Форма в блоке с картой")
                            ->options(
                                fn() => Former::all()->pluck(
                                    "name",
                                    "id"
                                )
                            )
                            ->searchable(),
                        Select::make("buy_one_click")
                            ->label("Купить в один клик")
                            ->options(
                                fn() => Former::all()->pluck(
                                    "name",
                                    "id"
                                )
                            )
                            ->searchable(),
                    ]),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function getActions(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if ($tenant) {
            return [
                // Action::make('sitemap')
                //     ->requiresConfirmation()
                //     ->action(fn () => $this->generateSitemap())
                //     ->label(trans('filament-settings-hub::messages.settings.site.site-map')),
                Action::make("back")
                    ->action(
                        fn() => redirect()->route(
                            "filament." .
                                filament()->getCurrentPanel()->getId() .
                                ".pages.settings-hub",
                            $tenant
                        )
                    )
                    ->color("danger")
                    ->label(trans("filament-settings-hub::messages.back")),
            ];
        }

        return [
            // Action::make('sitemap')
            //     ->requiresConfirmation()
            //     ->action(fn () => $this->generateSitemap())
            //     ->label(trans('filament-settings-hub::messages.settings.site.site-map')),
            Action::make("back")
                ->action(
                    fn() => redirect()->route(
                        "filament." .
                            filament()->getCurrentPanel()->getId() .
                            ".pages.settings-hub"
                    )
                )
                ->color("danger")
                ->label(trans("filament-settings-hub::messages.back")),
        ];
    }
}
