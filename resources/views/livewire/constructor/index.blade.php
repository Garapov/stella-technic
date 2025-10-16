<div class="h-full grid grid-cols-12 relative" x-data="construct({
    selectedColor: 'blue',
    debugMode: false,
    desks: @js(array(
        'low_slim' => \App\Models\ProductVariant::where('id', setting('deck_low_slim'))->first(),
        'high_slim' => \App\Models\ProductVariant::where('id', setting('deck_high_slim'))->first(),
        'low_wide' => \App\Models\ProductVariant::where('id', setting('deck_low_wide'))->first(),
        'high_wide' => \App\Models\ProductVariant::where('id', setting('deck_high_wide'))->first(),
    )),
    boxes: @js(array(
        'box_small_red' => \App\Models\ProductVariant::where('id', setting('box_small_red'))->first(),
        'box_small_green' => \App\Models\ProductVariant::where('id', setting('box_small_green'))->first(),
        'box_small_blue' => \App\Models\ProductVariant::where('id', setting('box_small_blue'))->first(),
        'box_small_yellow' => \App\Models\ProductVariant::where('id', setting('box_small_yellow'))->first(),
        'box_small_gray' => \App\Models\ProductVariant::where('id', setting('box_small_gray'))->first(),
        'box_medium_red' => \App\Models\ProductVariant::where('id', setting('box_medium_red'))->first(),
        'box_medium_green' => \App\Models\ProductVariant::where('id', setting('box_medium_green'))->first(),
        'box_medium_blue' => \App\Models\ProductVariant::where('id', setting('box_medium_blue'))->first(),
        'box_medium_yellow' => \App\Models\ProductVariant::where('id', setting('box_medium_yellow'))->first(),
        'box_medium_gray' => \App\Models\ProductVariant::where('id', setting('box_medium_gray'))->first(),
        'box_large_red' => \App\Models\ProductVariant::where('id', setting('box_large_red'))->first(),
        'box_large_green' => \App\Models\ProductVariant::where('id', setting('box_large_green'))->first(),
        'box_large_blue' => \App\Models\ProductVariant::where('id', setting('box_large_blue'))->first(),
        'box_large_yellow' => \App\Models\ProductVariant::where('id', setting('box_large_yellow'))->first(),
        'box_large_gray' => \App\Models\ProductVariant::where('id', setting('box_large_gray'))->first(),
    )),
    addedRows: $wire.entangle('added_rows'),
    embeded: $wire.entangle('embeded'),
    selectedWidth: $wire.entangle('selectedWidth'),
    selectedHeight: $wire.entangle('selectedHeight'),
    selectedDeskType: $wire.entangle('selectedDeskType'),
    selectedPosition: $wire.entangle('selectedPosition')
})" >
    <x-constructor.projection />
    <x-constructor.buttons :products="$products" :param_items="$param_items" :selected_params="$selected_params" />
    <x-constructor.scene />    
</div>
