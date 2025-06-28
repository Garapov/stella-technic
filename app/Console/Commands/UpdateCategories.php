<?php

namespace App\Console\Commands;

use App\Models\ProductCategory;
use App\Services\CategoryUpdater;
use Illuminate\Console\Command;

class UpdateCategories extends Command
{
    protected $signature = 'update:categories {--delay=200}';
    protected $description = 'Обновление данных категорий со старого сайта (не будет работать после переноса)';

    protected $updater;

    public function __construct(CategoryUpdater $updater)
    {
        parent::__construct();
        $this->updater = $updater;
    }

    public function handle()
    {
        $this->newLine(1);
        $this->info('Начало обновления категорий...');
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
            $this->updater->updateCategory($title);

            // Продвигаем прогресс
            $bar->advance();

            // Пауза между запросами
            usleep($delay * 1000); // milliseconds → microseconds
        }

        $bar->setMessage("\n\nВсе категории успешно обновлены.");

        $bar->finish();
        $this->newLine(2);
        $this->info('Обновление завершено.');
    }
}
