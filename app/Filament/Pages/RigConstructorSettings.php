<?php

namespace App\Filament\Pages;

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

use Filament\Pages\Page;

class RigConstructorSettings extends SiteSettings
{
    protected static string $settings = ConstructorSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Tabs::make("Tabs")
                ->tabs([
                    Tabs\Tab::make("Стойки")->schema([
                        Select::make("deck_low_slim")
                            ->label("Стойка 735x1515")
                            ->options(
                                fn() => ProductVariant::all()->pluck(
                                    "name",
                                    "id"
                                )
                            )
                            ->searchable(),
                        Select::make("deck_high_slim")
                            ->label("Стойка 735x2020")
                            ->options(
                                fn() => ProductVariant::all()->pluck(
                                    "name",
                                    "id"
                                )
                            )
                            ->searchable(),
                        Select::make("deck_low_wide")
                            ->label("Стойка 1150x1515")
                            ->options(
                                fn() => ProductVariant::all()->pluck(
                                    "name",
                                    "id"
                                )
                            )
                            ->searchable(),
                        Select::make("deck_high_wide")
                            ->label("Стойка 1150x2020")
                            ->options(
                                fn() => ProductVariant::all()->pluck(
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
