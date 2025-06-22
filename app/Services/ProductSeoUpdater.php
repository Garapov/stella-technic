<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ProductVariant;

class ProductSeoUpdater
{
    public function updateProduct($product_sku)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get('https://stella-tech.ru/getter/products', [
                'sku' => $product_sku
            ]);

            if ($response->successful()) {
                $body = json_decode($response->body());

                if ($body->success) {
                    try {
                        ProductVariant::where('sku', $product_sku)->first()?->update([
                            'seo' => $body->data
                        ]);
                    } catch (\Exception $e) {
                        Log::error("DB error for SKU {$product_sku}: " . $e->getMessage());
                    }
                }

                Log::info("Success for SKU {$product_sku}: " . $response->body());
                return $body;
            } else {
                Log::error("Failed to fetch product data for SKU {$product_sku}. Status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Exception for SKU {$product_sku}: " . $e->getMessage());
        }
    }
}
