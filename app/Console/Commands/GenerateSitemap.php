<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:sitemap';

    protected $path = 'public/sitemap.xml';

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
        
        // dd(config('app.url'));
        SitemapGenerator::create(config('app.url'))->hasCrawled(function (Url $url) {
            $this->info($url->url);
            Http::get($url->url);
            return $url;
        })->writeToFile($this->path);
    }
}
