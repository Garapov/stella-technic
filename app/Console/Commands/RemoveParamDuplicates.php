<?php

namespace App\Console\Commands;

use App\Models\ProductParam;
use App\Models\ProductParamItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveParamDuplicates extends Command
{
    protected $signature = 'catalog:remove-param-duplicates {--param-name=} {--delay=200}';
    protected $description = 'Объединяет дубликаты ProductParam по name, переносит ProductParamItem и связи с ProductVariant и Product.';

    public function handle()
    {
        $this->newLine();
        $this->info('🔍 Начало обработки параметров...');

        $delay = (int)$this->option('delay');
        $paramName = $this->option('param-name');

        // Получаем параметры (все или один по имени)
        $query = ProductParam::query();
        if ($paramName) {
            $query->where('name', $paramName);
            $this->info("Фильтрация по имени параметра: {$paramName}");
        }

        $params = $query->get();
        if ($params->isEmpty()) {
            $this->warn('❌ Параметры не найдены.');
            return;
        }

        // Группировка по name
        $groups = $params->groupBy('name')->filter(fn ($g) => $g->count() > 1);

        if ($groups->isEmpty()) {
            $this->info('✅ Дубликатов не найдено.');
            return;
        }

        foreach ($groups as $name => $duplicates) {
            $this->line("\n➡️ Обработка дубликатов параметра: {$name}");

            $main = $duplicates->shift(); // основной параметр
            $this->line("   Основной параметр: ID {$main->id}");

            foreach ($duplicates as $duplicate) {
                DB::transaction(function () use ($main, $duplicate, $name) {
                    foreach ($duplicate->params as $item) {
                        // Проверяем, есть ли у основного такое же значение
                        $existing = ProductParamItem::where('product_param_id', $main->id)
                            ->where('value', $item->value)
                            ->first();

                        if ($existing) {
                            // Переносим связи ProductVariant
                            $variantIds = DB::table('product_variant_product_param_item')
                                ->where('product_param_item_id', $item->id)
                                ->pluck('product_variant_id');

                            foreach ($variantIds as $variantId) {
                                DB::table('product_variant_product_param_item')->updateOrInsert([
                                    'product_variant_id' => $variantId,
                                    'product_param_item_id' => $existing->id,
                                ], ['updated_at' => now()]);
                            }

                            // Переносим связи Product
                            $productIds = DB::table('product_product_param_item')
                                ->where('product_param_item_id', $item->id)
                                ->pluck('product_id');

                            foreach ($productIds as $productId) {
                                DB::table('product_product_param_item')->updateOrInsert([
                                    'product_id' => $productId,
                                    'product_param_item_id' => $existing->id,
                                ], ['updated_at' => now()]);
                            }

                            // Удаляем связи и сам дубль
                            DB::table('product_variant_product_param_item')->where('product_param_item_id', $item->id)->delete();
                            DB::table('product_product_param_item')->where('product_param_item_id', $item->id)->delete();

                            $item->delete();

                            Log::info("🗑 Удалено дублирующее значение '{$item->value}' параметра '{$name}'");
                        } else {
                            // Просто переносим item к основному параметру
                            $item->update(['product_param_id' => $main->id]);
                        }
                    }

                    // Удаляем сам дубль параметра
                    $duplicate->delete();
                    Log::info("✅ Удален дублирующий параметр '{$name}' (ID {$duplicate->id})");
                });

                usleep($delay * 1000);
            }
        }

        $this->newLine(2);
        $this->info('🎯 Объединение дубликатов завершено.');
    }
}
