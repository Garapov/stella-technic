<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use CmsMulti\FilamentClearCache\FilamentClearCachePlugin;
use Datlechin\FilamentMenuBuilder\MenuPanel\ModelMenuPanel;
use App\Filament\Plugins\BlogPlugin;
use App\Filament\Plugins\MenuBuilderPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentImageLibrary\Filament\Plugins\FilamentImageLibraryPlugin;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Z3d0X\FilamentFabricator\FilamentFabricatorPlugin;
use App\Filament\Pages\ImportProducts;
use JibayMcs\FilamentTour\FilamentTourPlugin;
use Rupadana\ApiService\ApiServicePlugin;
use Datlechin\FilamentMenuBuilder\MenuPanel\StaticMenuPanel;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentSettingsHub\Facades\FilamentSettingsHub;
use TomatoPHP\FilamentSettingsHub\Services\Contracts\SettingHold;
use App\Filament\Pages\GeneralSettings;
use App\Filament\Pages\SocialSettings;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;

class AdminPanelProvider extends PanelProvider
{
    public function boot(Panel $panel)
    {
        FilamentSettingsHub::register([
            SettingHold::make()
                ->page(GeneralSettings::class)
                ->order(0)
                ->label("filament-settings-hub::messages.settings.site.title")
                ->icon("heroicon-o-globe-alt")
                ->description(
                    "filament-settings-hub::messages.settings.site.description"
                )
                ->group("filament-settings-hub::messages.group"),
            SettingHold::make()
                ->page(SocialSettings::class)
                ->order(0)
                ->label("filament-settings-hub::messages.settings.social.title")
                ->icon("heroicon-s-bars-3")
                ->description(
                    "filament-settings-hub::messages.settings.social.description"
                )
                ->group("filament-settings-hub::messages.group"),
        ]);

        $panel->renderHook(
            name: "panels::user-menu.before",
            hook: fn(): string => Blade::render("adasdasdasdads")
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->viteTheme("resources/css/filament/admin/theme.css")
            ->id("admin")
            ->path("admin")
            ->login()
            ->databaseNotifications()
            ->colors([
                "primary" => Color::Amber,
            ])
            ->discoverResources(
                in: app_path("Filament/Resources"),
                for: "App\\Filament\\Resources"
            )
            ->discoverPages(
                in: app_path("Filament/Pages"),
                for: "App\\Filament\\Pages"
            )
            ->pages([Pages\Dashboard::class, ImportProducts::class])
            ->discoverWidgets(
                in: app_path("Filament/Widgets"),
                for: "App\\Filament\\Widgets"
            )
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make("Телескоп")
                    ->url("/telescope", shouldOpenInNewTab: true)
                    ->icon("fas-file-lines")
                    ->group("Аналитика"),
                NavigationItem::make("Документация")
                    ->url("/docs/api", shouldOpenInNewTab: true)
                    ->icon("fas-file-lines")
                    ->group("Аналитика"),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class])
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                BlogPlugin::make(),
                FilamentClearCachePlugin::make(),
                FilamentFabricatorPlugin::make(),
                // FilamentImageLibraryPlugin::make(),
                FilamentTourPlugin::make(),
                ApiServicePlugin::make(),
                FilamentSpatieRolesPermissionsPlugin::make(),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        userMenuLabel: "Мой профиль", // Customizes the 'account' link label in the panel User Menu (default = null)
                        shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        navigationGroup: "User", // Sets the navigation group for the My Profile page (default = null)
                        hasAvatars: false, // Enables the avatar upload form component (default = false)
                        slug: "my-profile" // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->withoutMyProfileComponents(["update_password"])
                    ->enableTwoFactorAuthentication(),
                MenuBuilderPlugin::make()
                    ->addLocations([
                        "top_menu" => "Верхнее меню",
                        "search_menu" => "Поисковое меню",
                        "header" => "Главное меню",
                        "footer" => "Меню в подвале",
                    ])
                    ->addMenuPanels([
                        ModelMenuPanel::make()
                            ->model(\App\Models\Page::class)
                            ->paginate(perPage: 5, condition: true),
                        ModelMenuPanel::make()
                            ->model(\App\Models\Post::class)
                            ->paginate(perPage: 5, condition: true),
                        ModelMenuPanel::make()
                            ->model(\App\Models\ProductCategory::class)
                            ->paginate(perPage: 15, condition: true),
                        StaticMenuPanel::make("Статичные страницы")
                            ->paginate(perPage: 5, condition: true)
                            ->add("Бренды", function () {
                                return route("client.brands.index");
                            })
                            ->add("Популярные товары", function () {
                                return route("client.catalog.popular");
                            })
                            ->add("Статьи", function () {
                                return route("client.articles.index");
                            })
                            ->add("Блог", function () {
                                return route("client.posts.index");
                            })
                            ->add("Сертификаты", function () {
                                return route("client.certificates");
                            })
                            ->add("Вакансии", function () {
                                return route("client.vacancies");
                            })
                            ->add("Сотрудники", function () {
                                return route("client.workers");
                            })
                            ->add("Конструктор стеллажей", function () {
                                return route("client.constructor");
                            }),
                    ]),
                FilamentSettingsHubPlugin::make()
                    ->allowSiteSettings(false)
                    ->allowSocialMenuSettings(false),
            ])
            ->assets([
                // Css::make('custom-stylesheet', resource_path('css/custom.css')),
                // Js::make('yandex-maps-api-v3', 'https://api-maps.yandex.ru/2.1/?apikey='. config('services.maps.key') . '&lang=ru_RU&suggest_apikey=' . config('services.maps.suggestion_key')),
            ]);
    }
}
