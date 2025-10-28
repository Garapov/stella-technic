<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class ProductSelector
{
    protected array $memo = [];

    public function fromCategory(?ProductCategory $category): Collection
    {
        if (!$category) {
            return collect();
        }

        $key = "category_" . $category->id;
        if (isset($this->memo[$key])) {
            return $this->memo[$key];
        }

        switch ($category->type) {
            case "duplicator":
                $result = is_array($category->duplicate_id) && count($category->duplicate_id)
                ? ProductCategory::with('products:id')
                    ->whereIn('id', $category->duplicate_id)
                    ->get()
                    ->pluck('products') // получаем коллекции продуктов
                    ->flatten()         // объединяем в одну плоскую коллекцию
                    ->pluck('id')       // получаем только ID продуктов
                : collect();
                break;

            case "filter":
                $paramItemIds = $category->paramItems->pluck("id");

                
                $byParamItems = $this->variantsByRelation(
                    "paramItems",
                    $paramItemIds,
                    $category->duplicate_id,
                    $category
                );
                $byParametrs = $this->variantsByRelation(
                    "parametrs",
                    $paramItemIds,
                    $category->duplicate_id,
                    $category
                );

                
                $result = $byParamItems->merge($byParametrs)->unique();
                // dd($result);
                // dd($result);
                break;

            case "variations":
                $result = $category->variations->pluck("id");
                break;

            default:
                $allCategoryIds = collect();

                $collectIds = function ($category) use (&$collectIds, &$allCategoryIds) {
                    $allCategoryIds->push($category->id);

                    foreach ($category->categories as $child) {
                        $collectIds($child);
                    }
                };

                $collectIds($category);

                $result = Product::whereIn('category_id', $allCategoryIds)
                    ->orWhereHas('categories', function ($q) use ($allCategoryIds) {
                        $q->whereIn('product_categories.id', $allCategoryIds);
                    })
                    ->pluck('id')
                    ->unique();
                // dd($result);
                break;
        }

        return $this->memo[$key] = $result;
    }

    public function fromBrandSlug(string $slug): Collection
    {
        return Brand::where("slug", $slug)
            ->with("products:id")
            ->first()
            ?->products->pluck("id") ?? collect();
    }

    protected function variantsByRelation(
        string $relation,
        Collection $ids,
        $category_id = null,
        $category = null
    ): Collection {
        // if ($category->params_to_one) {
        //     // Загружаем обе связи
        //     $query = ProductVariant::with(['paramItems:id', 'parametrs:id']);
            
        //     if ($category_id) {
        //         $query->whereHas('product.categories', function ($q) use ($category_id) {
        //             $q->whereIn('product_categories.id', (array) $category_id);
        //         });
        //     }

        //     return $query->get()->filter(function ($variant) use ($ids) {
        //         // Объединяем id из обеих связей
        //         $allParamIds = $variant->paramItems->pluck('id')
        //             ->merge($variant->parametrs->pluck('id'))
        //             ->unique();

        //         // Проверяем, все ли нужные ids найдены
        //         return $ids->diff($allParamIds)->isEmpty();
        //     })->pluck('id');
        // }
        // Старая логика: достаточно совпадения в одной связи
        $variants = ProductVariant::with('product')->whereHas($relation, function ($q) use ($ids, $relation) {
            if ($relation === 'paramItems') {
                $q->whereIn('product_variant_product_param_item.product_param_item_id', $ids->toArray());
            } else {
                $q->whereIn('variation_product_param_item.product_param_item_id', $ids->toArray());
            }
        })
        ->tap(function ($query) use ($relation) {
            // Логируем SQL и параметры
            Log::debug('ProductVariant::whereHas SQL', [
                'relation' => $relation,
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
            ]);
        })->get();

        // Логируем результат
        Log::debug('ProductVariant::whereHas result', [
            'count' => $variants->count(),
            'ids' => $variants,
        ]);

        $products = $variants
            ->pluck('product')
            ->filter() // убирает null
            ->unique('id')
            ->values()
            ->pluck('id');
            // ->when($category_id, function ($query) use ($category_id) {
            //     $query->whereHas('product.categories', function ($q) use ($category_id) {
            //         $q->whereIn("product_categories.id", (array) $category_id);
            //     });
            // })
            
        // dd($products);
        return $products;
    }
}
