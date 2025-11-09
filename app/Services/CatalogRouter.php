<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;

class CatalogRouter
{
    public function resolve($path)
    {
        $segments = explode('/', $path);
        
        

        $productSlug = null;
        $categories = ProductCategory::all();
        $category = null;

        $parent = null;
        foreach ($segments as $i => $segment) {
            $possibleCategory = $categories->where('slug', $segment)->first();

            if ($possibleCategory) {
                $category = $possibleCategory;
            } else {
                $productSlug = $segment;
                break;
            }
        }

        if ($productSlug) {
            
            $product = Cache::rememberForever('product-detail:slug_' . $productSlug, function () use ($productSlug) {
                return ProductVariant::where('slug', $productSlug)->with([
                    'product',
                    'paramItems',
                    'parametrs',
                    'paramItems.productParam',
                    'parametrs.productParam',
                    'product.categories',
                    'product.categories',
                    'product.variants',
                    'product.variants.paramItems',
                    'product.variants.paramItems.productParam',
                    'product.brand',
                    'crossSells',
                    'upSells',
                ])->first();
            });
            

            if (!$product) {
                abort(404);
            }

            // dd([
            //     'product_slug' => $product->slug,
            //     'path' => $category->urlChain(),
            // ]);

            // Используем конкретный контроллер
            return view('client.product_detail', [
                'variation' => $product,
            ]);
        }

        if ($category) {
            return view('client.catalog', [
                'path' => $path,
            ]);
        }

        abort(404);
    }
}