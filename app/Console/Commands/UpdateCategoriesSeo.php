<?php

namespace App\Console\Commands;

use App\Models\ProductCategory;
use App\Services\CategorySeoUpdater;
use Illuminate\Console\Command;

class UpdateCategoriesSeo extends Command
{
    protected $signature = 'seo:update-categories-seo {--delay=200}';
    protected $description = 'Update SEO data for all categories, sequentially with progress';

    protected $seoUpdater;

    public function __construct(CategorySeoUpdater $seoUpdater)
    {
        parent::__construct();
        $this->seoUpdater = $seoUpdater;
    }

    public function handle()
    {
        $this->newLine(1);
        $this->info('Starting SEO update for categories...');
        $this->newLine(1);
        $delay = (int) $this->option('delay');
        $categories = ProductCategory::all();

        $bar = $this->output->createProgressBar($categories->count());
        $bar->setFormat("%percent:3s%% [%bar%] %current% из %max% %message%");
        $bar->start();

        foreach ($categories as $category) {
            $title = $category->title;

            $bar->setMessage("\n\nОбрабатывается: {$category->title}\n");

            // Обновляем SEO
            $this->seoUpdater->updateCategory($title);

            // Продвигаем прогресс
            $bar->advance();

            // Пауза между запросами
            usleep($delay * 1000); // milliseconds → microseconds
        }

        $bar->setMessage("\n\nВсе категории успешно обновлены.");

        $bar->finish();
        $this->newLine(2);
        $this->info('SEO update completed.');
    }
}
