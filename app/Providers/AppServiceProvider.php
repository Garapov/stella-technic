<?php

namespace App\Providers;

use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Z3d0X\FilamentFabricator\Resources\PageResource;
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
        Gate::before(function ($user, $ability) {
            return $user->hasTokenPermission($ability, $user) ?: null;
        });

        Queue::failing(function (JobFailed $event) {
            $import = $event->job->payload()['import'] ?? null;
            if ($import) {
                Import::find($import->id)?->update([
                    'status' => 'failed',
                    'error' => $event->exception->getMessage()
                ]);
            }
        });
        PageResource::navigationGroup('Страницы');
    }
}
