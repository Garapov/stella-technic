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
        if (!$category) return collect();

        $key = 'category_' . $category->id;
        if (isset($this->memo[$key])) return $this->memo[$key];

        switch ($category->type) {
            case 'duplicator':
                $result = $category->duplicate_id
                    ? ProductCategory::with('products:id')->find($category->duplicate_id)?->products->pluck('id') ?? collect()
                    : collect();
                break;

            case 'filter':
                $paramItemIds = $category->paramItems->pluck('id');
                $byParamItems = $this->variantsByRelation('paramItems', $paramItemIds);
                $byParametrs = $this->variantsByRelation('parametrs', $paramItemIds);
                $result = $byParamItems->merge($byParametrs)->unique();
                break;

            case 'variations':
                $result = $category->variations->pluck('id');
                break;

            default:
                $result = $category->products->pluck('id');
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

    protected function variantsByRelation(string $relation, Collection $ids): Collection
    {
        return ProductVariant::whereHas($relation, function ($q) use ($ids) {
            $q->whereIn('product_param_items.id', $ids);
        })->pluck('id');
    }
}