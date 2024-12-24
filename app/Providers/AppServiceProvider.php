<?php

namespace App\Providers;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
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
        Queue::failing(function (JobFailed $event) {
            $import = $event->job->payload()['import'] ?? null;
            if ($import) {
                Import::find($import->id)?->update([
                    'status' => 'failed',
                    'error' => $event->exception->getMessage()
                ]);
            }
        });
    }
}
