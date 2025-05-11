<div class="h-full grid grid-cols-9" x-data="construct({
    selectedColor: 'blue',
    debugMode: false,
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
    addedRows: @js($added_rows)
})" >
    <x-constructor.projection />
    <x-constructor.buttons />
    <x-constructor.scene />
</div>
