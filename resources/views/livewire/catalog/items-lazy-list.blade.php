<div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($this->variations as $variation)
            @livewire('general.product-variant', [
                'variant' => $variation,
                'category' => $category ?? null,
            ], key('variant_' . $variation->id))
        @endforeach
    </div>

    {{ $this->variations->links() }}
</div>
