<?php

namespace App\Services;

use App\Models\ProductParam;
use App\Models\ProductParamItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ProductUpdater
{
    public $params = [
        ['code' => 'contact_material', 'name' => 'Материал контактной части'],
        ['code' => 'kol_radius_gabaritnyh_razmerov_tormoza', 'name' => 'Радиус габаритных размеров тормоза'],
        ['code' => 'disccore_material', 'name' => 'Материал сердечника диска'],
        ['code' => 'bearing_type', 'name' => 'Тип подшипника'],
        ['code' => 'bracket_type', 'name' => 'Тип кронштейна'],
        ['code' => 'bracket_model', 'name' => 'Модель кронштейна (опоры)'],
        ['code' => 'kol_diametr_stupicy', 'name' => 'Диаметр ступицы'],
        ['code' => 'kol_dlina_stupicy', 'name' => 'Ширина ступицы'],
        ['code' => 'kol_diametr_kolesa', 'name' => 'Диаметр колеса'],
        ['code' => 'gruz_t_kg', 'name' => 'Грузоподъемность'],
        ['code' => 'kol_shirina_kolesa_mm', 'name' => 'Ширина колеса'],
        ['code' => 'kol_vysota_kolesa_mm', 'name' => 'Высота колеса'],
        ['code' => 'kol_platforma', 'name' => 'Габариты площадки'],
        ['code' => 'kol_mezhosevoe_rasstoyanie_krepezhnyh_otverstij', 'name' => 'Межосевое расстояние крепежных отверстий'],
        ['code' => 'kol_smeshchenie_osej', 'name' => 'Смещение осей'],
        ['code' => 'diametr_gnezda_podshipnikov', 'name' => 'Диаметр гнезда подшипника'],
        ['code' => 'glubina_gnezda_podshipnikov', 'name' => 'Глубина гнезда подшипника'],
        ['code' => 'fastening', 'name' => 'Крепление'],
        ['code' => 'kol_ves_kolesa_kg', 'name' => 'Вес'],
        ['code' => 'ves_kg', 'name' => 'Вес'],
        ['code' => 'kol_diametr_paneli', 'name' => 'Диаметр площадки'],
        ['code' => 'diametr_krepezhnogo_otverstiya', 'name' => 'Диаметр крепежного отверстия'],
        ['code' => 'country_of_origin', 'name' => 'Страна производства'],
        ['code' => 'temperature', 'name' => 'Температура эксплуатации, °С'],
        ['code' => 'sphere', 'name' => 'Сфера'],
        ['code' => 'setup_type', 'name' => 'Тип установки'],
        ['code' => 'tip_tovara_box', 'name' => 'Тип ящиков в системе хранения'],
        ['code' => 'kol_gruzopod_t_kolesa_kg_6_km_ch', 'name' => 'Грузоподъемность до 6 км/ч'],
        ['code' => 'maks_diametr_osi_mm', 'name' => 'Максимальный диаметр ступицы'],
        ['code' => 'naruzhnyj_diametr_stupicy_mm', 'name' => 'Наружный диаметр ступицы'],
    ];

    public function updateProduct($variant)
    {
        $body = $this->fetchProductData($variant->sku);
        if (!$body || empty($body->success) || empty($body->data)) {
            Log::error("No valid product data for SKU {$variant->sku}");
            return null;
        }

        $this->updateVariantFields($variant, $body->data);

        // Синхронизация параметров
        foreach ($this->params as $param) {
            $this->safeSyncParam($variant, $body->data, $param['code'], $param['name']);
        }

        return $body;
    }

    protected function fetchProductData($sku)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get('https://stella-tech.ru/getter/products', [
                'sku' => $sku
            ]);
            if ($response->successful()) {
                $body = json_decode($response->body());
                return $body;
            } else {
                Log::error("Failed to fetch product data for SKU {$sku}. Status: " . $response->status());
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Exception while fetching product data for SKU {$sku}: " . $e->getMessage());
            return null;
        }
    }

    protected function updateVariantFields($variation, $data)
    {
        try {
            $variation->update([
                'seo' => $data->seo ?? [],
                'uuid' => $data->uuid ?? null,
                'price' => $data->price ?? $variation->price,
                'slug' => $data->slug ?? $variation->slug
            ]);
        } catch (\Exception $e) {
            Log::error("Exception while updating ProductVariant fields for SKU {$variation->sku}: " . $e->getMessage());
        }
    }

    protected function safeSyncParam($variation, $data, $param_code, $param_name)
    {
        try {
            $this->syncParam($variation, $data, $param_code, $param_name);
        } catch (\Exception $e) {
            Log::error("Exception in syncParam for SKU {$variation->sku}, param {$param_code}: " . $e->getMessage());
        }
    }


    public function syncParam(ProductVariant $variation, object $data, string $param_code, string $param_name): void
    {
        if (!property_exists($data, $param_code) || empty($data->$param_code)) return;

        if (empty($param_name)) return;

        

        $param = ProductParam::firstOrCreate([
            'name' => $param_name,
            'type' => 'checkboxes'
        ]);

        $key_param_items = $variation->paramItems()->where('product_param_items.product_param_id', $param->id)->get();

        if ($key_param_items->count() > 0) {
            Log::info("Параметр '{$param_name}' ключевой и существует SKU {$variation->sku}, пропускаем.");
            return;
        }; 

        $currentItems = $variation->parametrs()->where('product_param_items.product_param_id', $param->id)->get();

        Log::info("Найденные '{$param_name}' для SKU {$variation->sku}: " . json_encode( $currentItems->pluck('title', 'productParam.name')->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Удаляем все старые связи этого параметра у товара
        if ($currentItems->count() > 0) {
            $variation->parametrs()->detach($currentItems->pluck('id')->toArray());
        }

        $value = Str::of($data->$param_code)->transliterate()->toString();

        Log::info("Пытаемся создать или найти параметр SKU {$variation->sku}: " . json_encode( [$param_name => $data->$param_code], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ));

        $param_item = ProductParamItem::updateOrCreate(
            [
                'title' => $data->$param_code,
                'product_param_id' => $param->id
            ],
            [
                'value' => Str::snake($value),
                'product_param_id' => $param->id,
            ]
        );

        Log::info("Пытаемся обновить параметр SKU {$variation->sku}: " . json_encode( [$param_item->productParam->name => $param_item->title], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ));

        // Привязываем только актуальное значение
        try {
            $variation->parametrs()->attach($param_item->id);
        } catch (\Exception $e) {
            Log::error("Attach error for SKU {$variation->sku}, param_item_id {$param_item->id}: " . $e->getMessage());
        }
    }
}
