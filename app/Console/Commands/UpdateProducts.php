<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductVariant;
use App\Services\ProductUpdater;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

class UpdateProducts extends Command
{
    protected $signature = 'update:products {--delay=200}';
    protected $description = 'Обновление данных товаров со старого сайта (не будет работать после переноса)';

    protected $updater;

    public function __construct(ProductUpdater $updater)
    {
        parent::__construct();
        $this->updater = $updater;
    }

    public function handle()
    {
        $this->newLine(1);
        $this->info('Начало обновления товаров...');
        $this->newLine(1);

        $delay = (int) $this->option('delay');
        $variants = ProductVariant::all();

        $bar = $this->output->createProgressBar($variants->count());
        $bar->setFormat("%percent:3s%% [%bar%] %current% из %max% %message%");
        $bar->start();

        foreach ($variants as $variant) {
            $sku = $variant->sku;

            $bar->setMessage("\n\nОбрабатывается: {$variant->name} ({$variant->sku})\n");

            // Обновляем SEO
            $this->updater->updateProduct($sku);

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
