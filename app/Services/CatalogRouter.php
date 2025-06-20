<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Models\ProductVariant;

class CatalogRouter
{
    public function resolve($path)
    {
        $segments = explode('/', $path);
        

        $productSlug = null;
        $category = null;

        $parent = null;
        foreach ($segments as $i => $segment) {
            $possibleCategory = ProductCategory::where('slug', $segment)->first();

            if ($possibleCategory) {
                $category = $possibleCategory;
            } else {
                $productSlug = $segment;
                break;
            }
        }

        
        if ($productSlug && $category) {
            
            $product = ProductVariant::where('slug', $productSlug)->first();

            if (!$product) {
                abort(404);
            }

            // dd([
            //     'product_slug' => $product->slug,
            //     'path' => $category->urlChain(),
            // ]);

            // Используем конкретный контроллер
            return view('client.product_detail', [
                'product_slug' => $product->slug,
                'path' => $category->urlChain(),
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