<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function update(Request $request)
    {
        Log::info(['Request: ',  $request]);
        $data = $request->json()->all();

        if (! isset($data['items']) || ! is_array($data['items'])) {
            return response()->json(['success' => false, 'message' => 'Invalid data format'], 400);
        }

        $processedCount = 0;
        $errors = [];

        foreach ($data['items'] as $item) {
            try {
                $this->processItem($item);
                $processedCount++;
            } catch (\Exception $e) {
                Log::error('Error processing item: '.json_encode($item).' Error: '.$e->getMessage());
                $errors[] = [
                    'guid' => $item['guid'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data processed',
            'data_count' => count($data['items']),
            'processed_count' => $processedCount,
            'errors' => $errors,
        ]);
    }

    private function processItem($item)
    {
        $guid = $item['guid'] ?? null;
        if (! $guid) {
            throw new \Exception('GUID is missing');
        }

        $price = 0;
        if (isset($item['prices']) && is_array($item['prices'])) {
            foreach ($item['prices'] as $p) {
                if (isset($p['price'])) {
                    $price = $p['price'];
                    break; // Take the first price found
                }
            }
        } elseif (isset($item['price'])) {
            $price = $item['price'];
        }
        $product = ProductVariant::where('uuid', $guid)->first();
        if ($product) {
            $product->update([
                'price' => $price,
                'count' => $item['available'] ?? 0,
            ]);

            return $product;
        }

        throw new \Exception("Product variant with UUID {$guid} not found");
    }
}
