<?php

namespace App\Console\Commands;

use App\Models\ProductCategory;
use Illuminate\Console\Command;
use App\Models\ProductVariant;
use App\Services\ProductSelector;
use App\Services\ProductUpdater;
use Illuminate\Support\Facades\Log;

class UpdateProducts extends Command
{
    protected $signature = 'update:products {--delay=200} {--category=false}';
    protected $description = 'Обновление данных товаров со старого сайта (не будет работать после переноса)';

    protected $updater;
    protected ProductSelector $selector;

    public function __construct(ProductUpdater $updater)
    {
        parent::__construct();
        $this->updater = $updater;
        $this->selector = new ProductSelector();
    }

    public function handle()
    {
        $this->newLine(1);
        $this->info('Начало обновления товаров...');
        $this->newLine(1);

        $delay = (int) $this->option('delay');
        $variants = ProductVariant::all();
        $category = ProductCategory::where('slug', $this->option('category'))->first();
        
        if ($category) {
            // Log::info("Обновление только категории {$category->title}");
            $product_ids = $this->selector->fromCategory($category);
            $variants = $variants->whereIn('product_id', $product_ids);
        }

        

        $bar = $this->output->createProgressBar($variants->count());
        $bar->setFormat("%percent:3s%% [%bar%] %current% из %max% %message%");
        $bar->start();

        foreach ($variants as $variant) {
            $sku = $variant->sku;

            $bar->setMessage("\n\nОбрабатывается: {$variant->name} ({$variant->sku})\n");

            Log::info("===================| {$variant->name} ({$variant->sku}) |=======================");

            // Обновляем SEO и параметры
            $this->updater->updateProduct($variant);

            Log::info("===================| {$variant->name} ({$variant->sku}) |=======================");

            // Продвигаем прогресс
            $bar->advance();

            // Пауза между запросами
            usleep($delay * 1000); // milliseconds → microseconds
        }

        $bar->setMessage("\n\nВсе товары успешно обновлены.");

        $bar->finish();
        $this->newLine(2);
        $this->info('Обновление завершено.');
    }
}
