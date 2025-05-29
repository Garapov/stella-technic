<?php

namespace App\Providers;

use App\Filament\Pages\RigConstructorSettings;
use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentSettingsHub\Facades\FilamentSettingsHub;
use TomatoPHP\FilamentSettingsHub\Services\Contracts\SettingHold;
use Z3d0X\FilamentFabricator\Resources\PageResource;
use CmsMulti\FilamentClearCache\Facades\FilamentClearCache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentClearCache::addCommand('responsecache:clear');
        
        Gate::before(function ($user, $ability) {
            return $user->hasTokenPermission($ability, $user) ?: null;
        });

        Queue::failing(function (JobFailed $event) {
            $import = $event->job->payload()["import"] ?? null;
            if ($import) {
                Import::find($import->id)?->update([
                    "status" => "failed",
                    "error" => $event->exception->getMessage(),
                ]);
            }
        });
        PageResource::navigationGroup("Страницы");

        FilamentSettingsHub::register([
            SettingHold::make()
                ->order(3)
                ->label("Конструктор стоек")
                ->icon("heroicon-o-cog")
                ->page(RigConstructorSettings::class)
                ->group("Конструкторы"),
        ]);

        
    }
}
