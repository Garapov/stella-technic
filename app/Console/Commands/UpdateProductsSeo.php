<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductVariant;
use App\Services\ProductSeoUpdater;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

class UpdateProductsSeo extends Command
{
    protected $signature = 'seo:update-products-seo {--delay=200}';
    protected $description = 'Update SEO data for all product variants, sequentially with progress';

    protected $seoUpdater;

    public function __construct(ProductSeoUpdater $seoUpdater)
    {
        parent::__construct();
        $this->seoUpdater = $seoUpdater;
    }

    public function handle()
    {
        $this->newLine(1);
        $this->info('Starting SEO update for product variants...');
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
            $this->seoUpdater->updateProduct($sku);

            // Продвигаем прогресс
            $bar->advance();

            // Пауза между запросами
            usleep($delay * 1000); // milliseconds → microseconds
        }

        $bar->setMessage("\n\nВсе товары успешно обновлены.");

        $bar->finish();
        $this->newLine(2);
        $this->info('SEO update completed.');
    }
}
