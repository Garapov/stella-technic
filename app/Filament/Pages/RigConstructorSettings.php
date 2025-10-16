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
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
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
                            ->options(function () {
                                return ProductVariant::query()
                                        ->get()
                                        ->mapWithKeys(function ($variation) {
                                            return [$variation->id => $variation->name . " ($variation->sku)"];
                                        });
                                })
                            ->searchable()
                            ->required(),
                        Select::make("deck_low_wide")
                            ->label("Стойка 1150x1515")
                            ->options(function () {
                                return ProductVariant::query()
                                        ->get()
                                        ->mapWithKeys(function ($variation) {
                                            return [$variation->id => $variation->name . " ($variation->sku)"];
                                        });
                                })
                            ->searchable()
                            ->required(),
                        Select::make("deck_high_wide")
                            ->label("Стойка 1150x2020")
                            ->options(function () {
                                return ProductVariant::query()
                                        ->get()
                                        ->mapWithKeys(function ($variation) {
                                            return [$variation->id => $variation->name . " ($variation->sku)"];
                                        });
                                })
                            ->searchable()
                            ->required(),
                    ]),
                    Tabs\Tab::make("Ящики")->schema([
                        Section::make('Ящик V1')
                            ->collapsible()
                            ->collapsed()
                            ->description('Привяжите ящики V1 согласно цветам')
                            ->schema([
                                Select::make("box_small_red")
                                    ->label("Красный")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_small_green")
                                    ->label("Зеленый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_small_blue")
                                    ->label("Синий")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_small_yellow")
                                    ->label("Желтый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_small_gray")
                                    ->label("Серый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                            ])
                            ->columns(1),
                       
                        Section::make('Ящик V2')
                            ->collapsible()
                            ->collapsed()
                            ->description('Привяжите ящики V2 согласно цветам')
                            ->schema([
                                Select::make("box_medium_red")
                                    ->label("Красный")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_medium_green")
                                    ->label("Зеленый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_medium_blue")
                                    ->label("Синий")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_medium_yellow")
                                    ->label("Желтый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_medium_gray")
                                    ->label("Серый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                            ])
                            ->columns(1),
                        Section::make('Ящик V3')
                            ->collapsible()
                            ->collapsed()
                            ->description('Привяжите ящики V3 согласно цветам')
                            ->schema([
                                Select::make("box_large_red")
                                    ->label("Красный")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_large_green")
                                    ->label("Зеленый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_large_blue")
                                    ->label("Синий")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_large_yellow")
                                    ->label("Желтый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make("box_large_gray")
                                    ->label("Серый")
                                    ->options(
                                        fn() => ProductVariant::all()->pluck(
                                            "name",
                                            "id"
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                            ])
                            ->columns(1),
                    ]),
                    Tabs\Tab::make("Дополнительные товары")->schema([
                        Select::make("deck_bracing")
                            ->label("Перегородка")
                             ->options(function () {
                                return ProductVariant::query()
                                        ->get()
                                        ->mapWithKeys(function ($variation) {
                                            return [$variation->id => $variation->name . " ($variation->sku)"];
                                        });
                                })
                            ->searchable()
                            ->required(),
                        Select::make("deck_stand")
                            ->label("Подставка")
                            ->options(function () { 
                                return ProductVariant::query()
                                        ->get()
                                        ->mapWithKeys(function ($variation) {
                                            return [$variation->id => $variation->name . " ($variation->sku)"];
                                        });
                                })
                            ->searchable()
                            ->required(),
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
