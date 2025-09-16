<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:sitemap';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generates a sitemap.xml for the application.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            SitemapGenerator::create(config('app.url'))
            
                ->hasCrawled(function (Url $url) {
                    $this->info($url->url);
                    Http::get($url->url);
                    return $url;
                })
                ->getSitemap()
                // here we add one extra link, but you can add as many as you'd like
                ->writeToFile(public_path('sitemap.xml'));
            Log::error('Sitemap success: ');
        } catch (\Throwable $e) {
            Log::error('Sitemap error: ' . $e->getMessage());
        }
    }
}
