<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class ProductSelector
{
    protected array $memo = [];

    public function fromCategory(?ProductCategory $category): Collection
    {
        if (! $category) {
            return collect();
        }

        $key = 'category_'.$category->id;
        if (isset($this->memo[$key])) {
            return $this->memo[$key];
        }

        switch ($category->type) {
            case 'duplicator':
                $product_ids = is_array($category->duplicate_id) && count($category->duplicate_id)
                ? ProductCategory::with('products:id')
                    ->whereIn('id', $category->duplicate_id)
                    ->get()
                    ->pluck('products') // получаем коллекции продуктов
                    ->flatten()           // объединяем в одну плоскую коллекцию
                    ->pluck('id')       // получаем только ID продуктов
                : collect();
                // dd(ProductCategory::with('products:id')
                //     ->whereIn('id', $category->duplicate_id)
                //     ->get());
                // dd($result);
                $result = ProductVariant::whereIn('product_id', $product_ids)->get();
                break;

            case 'filter':
                $paramItemIds = $category->paramItems->pluck('id');

                $byParamItems = $this->variantsByRelation(
                    'paramItems',
                    $paramItemIds,
                    $category->duplicate_id,
                    $category
                );
                $byParametrs = $this->variantsByRelation(
                    'parametrs',
                    $paramItemIds,
                    $category->duplicate_id,
                    $category
                );

                $result = $byParamItems->merge($byParametrs)->unique();
                // dd($result);
                // dd($result);
                break;

            case 'variations':
                $result = $category->variations;
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

                $product_ids = Product::whereIn('category_id', $allCategoryIds)
                    ->orWhereHas('categories', function ($q) use ($allCategoryIds) {
                        $q->whereIn('product_categories.id', $allCategoryIds);
                    })->pluck('id');

                $result = ProductVariant::whereIn('product_id', $product_ids)->get();
                // dd($result);
                break;
        }

        return $this->memo[$key] = $result;
    }

    public function fromBrandSlug(string $slug): Collection
    {
        return Brand::where('slug', $slug)
            ->with('products:id')
            ->first()
            ?->products->pluck('id') ?? collect();
    }

    protected function variantsByRelation(
        string $relation,
        Collection $ids,
        $category_id = null,
        $category = null
    ): Collection {
        $variants = ProductVariant::with('product')->whereHas($relation, function ($q) use ($ids, $relation) {
            if ($relation === 'paramItems') {
                $q->whereIn('product_variant_product_param_item.product_param_item_id', $ids->toArray());
            } else {
                $q->whereIn('variation_product_param_item.product_param_item_id', $ids->toArray());
            }
        })->get();

        return $variants;
    }
}
