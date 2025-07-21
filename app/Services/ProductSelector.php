<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;
use App\Models\ProductVariant;

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
                );
                $byParametrs = $this->variantsByRelation(
                    "parametrs",
                    $paramItemIds,
                    $category->duplicate_id,
                );
                $result = $byParamItems->merge($byParametrs)->unique();
                // dd($result);
                break;

            case "variations":
                $result = $category->variations->pluck("id");
                break;

            default:
                $result = $category->products->pluck("id");
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
    ): Collection {
        return ProductVariant::whereHas($relation, function ($q) use ($ids) {
            $q->whereIn("product_param_items.id", $ids);
        })
            ->when($category_id, function ($query) use ($category_id) {
                $query->whereHas("product.categories", function ($q) use (
                    $category_id,
                ) {
                    $q->where("product_categories.id", $category_id);
                });
            })
            ->pluck("id");
    }
}
