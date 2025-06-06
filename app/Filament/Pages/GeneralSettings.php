<?php

namespace App\Filament\Pages;

use App\Models\Page;
use App\Settings\SitesSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use TomatoPHP\FilamentSettingsHub\Pages\SiteSettings;
use Filament\Pages\Actions\Action;

class GeneralSettings extends SiteSettings
{
    protected static string $settings = SitesSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Tabs::make("Tabs")
                ->tabs([
                    Tabs\Tab::make("Основная информация")->schema([
                        TextInput::make("site_name")
                            ->required()
                            ->label(
                                trans(
                                    "filament-settings-hub::messages.settings.site.form.site_name"
                                )
                            )
                            ->columnSpan(2)
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_name")'
                                    : null
                            ),
                        Textarea::make("site_description")
                            ->label(
                                trans(
                                    "filament-settings-hub::messages.settings.site.form.site_description"
                                )
                            )
                            ->columnSpan(2)
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_description")'
                                    : null
                            ),
                        Textarea::make("site_keywords")
                            ->label(
                                trans(
                                    "filament-settings-hub::messages.settings.site.form.site_keywords"
                                )
                            )
                            ->columnSpan(2)
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_keywords")'
                                    : null
                            ),
                        FileUpload::make("site_profile")
                            ->directory("logos")
                            ->label(
                                trans(
                                    "filament-settings-hub::messages.settings.site.form.site_profile"
                                )
                            )
                            ->columnSpan(2)
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_profile")'
                                    : null
                            ),
                        FileUpload::make("site_logo")
                            ->directory("logos")
                            ->label(
                                trans(
                                    "filament-settings-hub::messages.settings.site.form.site_logo"
                                )
                            )
                            ->columnSpan(2)
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_logo")'
                                    : null
                            ),
                        TextInput::make("site_worktime")
                            ->label("Рабочее время")
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_worktime")'
                                    : null
                            ),
                    ]),
                    Tabs\Tab::make("Контакты")->schema([
                        TextInput::make("site_phone")
                            ->label(
                                trans(
                                    "filament-settings-hub::messages.settings.site.form.site_phone"
                                )
                            )
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_phone")'
                                    : null
                            ),
                        TextInput::make("site_secondphone")
                            ->label("Дополнительный номер")
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_secondphone")'
                                    : null
                            ),
                        // TextInput::make('site_author')
                        //     ->label(trans('filament-settings-hub::messages.settings.site.form.site_author'))
                        //     ->hint(config('filament-settings-hub.show_hint') ? 'setting("site_author")' : null),
                        TextInput::make("site_email")
                            ->label(
                                trans(
                                    "filament-settings-hub::messages.settings.site.form.site_email"
                                )
                            )
                            ->hint(
                                config("filament-settings-hub.show_hint")
                                    ? 'setting("site_email")'
                                    : null
                            ),
                    ]),
                    Tabs\Tab::make("Важные страницы")->schema([
                        Select::make("politics")
                            ->label(
                                "Страница политики обработки персональных данных"
                            )
                            ->options(fn() => Page::all()->pluck("title", "id"))
                            ->searchable(),
                        Select::make("cookies")
                            ->label("Страница политики обработки файлов cookie")
                            ->options(fn() => Page::all()->pluck("title", "id"))
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
