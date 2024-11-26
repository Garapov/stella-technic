<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariant;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        // Get the created product with its paramItems
        $product = $this->record->fresh(['paramItems']);
        
        // Now you can access paramItems
        $paramItems = $product->paramItems;
        
        // Do something with paramItems
        // dd($paramItems);

        foreach ($paramItems as $paramItem) {
            ProductVariant::create([
                'product_id' => $product->id,
                'product_param_item_id' => $paramItem->id,
                'name' => $product->name . ' ' . $paramItem->title,
                'price' => $product->price,
                'new_price' => $product->new_price,
                'image' => $product->image
            ]);
        }
    }
}
