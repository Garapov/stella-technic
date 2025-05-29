<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariant;
use Filament\Actions\ExportAction;
use App\Filament\Exports\ProductVariantExporter;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            ExportAction::make()
                ->exporter(ProductVariantExporter::class)
        ];
    }

    protected function afterSave(): void
    {
        // Get the created product with its paramItems
        // $product = $this->record->fresh(['links']);

        $this->record->makeProductVariations();

        $this->dispatch('refreshVariations');
        // dd($product);

        
        // // Get current paramItems IDs
        // $paramItemIds = $product->paramItems->pluck('id')->toArray();
        
        // // Get active variants
        // $activeVariants = ProductVariant::where('product_id', $product->id)
        //     ->pluck('product_param_item_id')
        //     ->toArray();

        // // Get deleted variants
        // $deletedVariants = ProductVariant::onlyTrashed()
        //     ->where('product_id', $product->id)
        //     ->pluck('product_param_item_id')
        //     ->toArray();

        // // Delete variants that are not in paramItems anymore
        // ProductVariant::where('product_id', $product->id)
        //     ->whereIn('product_param_item_id', array_diff($activeVariants, $paramItemIds))
        //     ->delete();

        // foreach ($product->paramItems as $paramItem) {
        //     // If variant exists but was deleted - restore it
        //     if (in_array($paramItem->id, $deletedVariants)) {
        //         ProductVariant::onlyTrashed()
        //             ->where('product_id', $product->id)
        //             ->where('product_param_item_id', $paramItem->id)
        //             ->restore();
        //     } 
        //     // If variant never existed - create it
        //     elseif (!in_array($paramItem->id, $activeVariants)) {
        //         ProductVariant::create([
        //             'product_id' => $product->id,
        //             'product_param_item_id' => $paramItem->id,
        //             'name' => $product->name . ' ' . $paramItem->title,
        //             'price' => $product->price,
        //             'new_price' => $product->new_price,
        //             'image' => $product->image
        //         ]);
        //     }
        // }

        // $this->js('window.location.reload()');
    }
}
