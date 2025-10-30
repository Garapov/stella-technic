<div class="xl:px-[100px] px-[20px] py-10">
    <div class="flex items-center justify-between mb-10">
        <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-10 w-1/5"></div>
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2">
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-10 w-10"></div>
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-10 w-10"></div>
            </div>
        </div>
    </div>
    <div class="grid lg:grid-cols-5 md:grid-cols-3 grid-cols-2 gap-4">
        @foreach (range(1,5) as $key=>$i)
            @include('placeholders.general.variation')
        @endforeach
    </div>
    {{-- <div class="glide__track" data-glide-el="track">
        <div class="glide__slides whitespace-normal">
            @foreach ($variations as $variation)
                @livewire('general.product-variant', [
                    'variant' => $variation
                ], key($variation->id))
            @endforeach
        </div>
    </div> --}}
</div>
