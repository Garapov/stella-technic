<?php

namespace App\Services;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CategoryUpdater
{
    public function updateCategory($category_title)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get('https://stella-tech.ru/getter/categories', [
                'pagetitle' => $category_title
            ]);

            if ($response->successful()) {
                $body = json_decode($response->body());

                // dd($body->data);

                if ($body->success) {
                    try {
                        $category = ProductCategory::where('title', $category_title)->first();

                        if (!$category) return;

                        $category->update([
                            'seo' => $body->data->seo ?? [],
                            'slug' => $body->data->slug ?? $category->slug,
                            'h1' => $body->data->h1 ?? $category->h1
                        ]);
                    } catch (\Exception $e) {
                        Log::error("DB error for TITLE {$category_title}: " . $e->getMessage());
                    }
                }

                Log::info("Success for TITLE {$body->data->pagetitle}: " . $response->body());
                return $body;
            } else {
                Log::error("Failed to fetch category data for TITLE {$category_title}. Status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Exception for TITLE {$category_title}: " . $e->getMessage());
        }
    }
}
