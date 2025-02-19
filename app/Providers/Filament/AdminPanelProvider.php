<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use CmsMulti\FilamentClearCache\FilamentClearCachePlugin;
use Datlechin\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
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
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentImageLibrary\Filament\Plugins\FilamentImageLibraryPlugin;
// use Stephenjude\FilamentBlog\BlogPlugin;
use Z3d0X\FilamentFabricator\FilamentFabricatorPlugin;
use App\Filament\Pages\ImportProducts;
use JibayMcs\FilamentTour\FilamentTourPlugin;
use Rupadana\ApiService\ApiServicePlugin;
use App\Models\ProductCategory;
use Datlechin\FilamentMenuBuilder\MenuPanel\StaticMenuPanel;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                ImportProducts::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('Телескоп')
                    ->url('/telescope', shouldOpenInNewTab: true)
                    ->icon('fas-file-lines')
                    ->group('Аналитика'),
                NavigationItem::make('Документация')
                    ->url('/docs/api', shouldOpenInNewTab: true)
                    ->icon('fas-file-lines')
                    ->group('Аналитика')
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                BlogPlugin::make(),
                FilamentClearCachePlugin::make(),
                FilamentFabricatorPlugin::make(),
                FilamentImageLibraryPlugin::make(),
                FilamentTourPlugin::make(),
                ApiServicePlugin::make(),
                FilamentSpatieRolesPermissionsPlugin::make(),
                MenuBuilderPlugin::make()
                    ->addLocations([
                        'top_menu' => 'Верхнее меню',
                        'search_menu' => 'Поисковое меню',
                        'header' => 'Главное меню',
                        'footer' => 'Меню в подвале',
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
                            ->paginate(perPage: 5, condition: true),
                        StaticMenuPanel::make('Статичные страницы')
                            ->paginate(perPage: 5, condition: true)
                            ->add('Бренды', function() {return route('client.brands.index');})
                            ->add('Популярные товары', function() {return route('client.catalog.popular');})
                            ->add('Статьи', function() {return route('client.articles.index');})
                            ->add('Блог', function() {return route('client.posts.index');})
                            ->add('Сертификаты', function() {return route('client.certificates');})
                            ->add('Вакансии', function() {return route('client.vacancies');})
                    ])
            ])
            ->spa();
    }
}
