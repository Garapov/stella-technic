<div class="h-full grid grid-cols-9" x-data="construct({
    selectedColor: 'blue',
    debugMode: true,
    desks: @js(array(
        'low_slim' => \App\Models\ProductVariant::where('id', setting('deck_low_slim'))->first(),
        'high_slim' => \App\Models\ProductVariant::where('id', setting('deck_high_slim'))->first(),
        'low_wide' => \App\Models\ProductVariant::where('id', setting('deck_low_wide'))->first(),
        'high_wide' => \App\Models\ProductVariant::where('id', setting('deck_high_wide'))->first(),
    )),
    boxes: @js(array(
        'box_small' => \App\Models\ProductVariant::where('id', setting('box_small'))->first(),
        'box_medium' => \App\Models\ProductVariant::where('id', setting('box_medium'))->first(),
        'box_large' => \App\Models\ProductVariant::where('id', setting('box_large'))->first(),
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
