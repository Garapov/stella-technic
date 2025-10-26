<div>
    @php
        $count = \App\Models\ProductVariant::query()
            ->join('product_variant_product_param_item as pvppi', 'pvppi.product_variant_id', '=', 'product_variants.id')
            ->join('product_param_items', 'product_param_items.id', '=', 'pvppi.product_param_item_id')
            ->where('product_param_items.product_param_id', $getRecord()->id)
            ->distinct('product_variants.id')
            ->count('product_variants.id');
    @endphp
    {{-- {{ print_r($getRecord()->params); }} --}}
    {{ $count }}
</div>
