<div>
    @if ($q != '' && $results['products']->count() > 0)
        @if ($results['products']->count() > 0)
            <h2 class="text-lg font-bold mb-2">Товары</h2>
            <div class="grid gap-4 sm:grid-cols-1 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($results['products'] as $product)
                    @livewire(
                        'general.product',
                        [
                            'product' => $product,
                        ],
                        key($product->id)
                    )
                @endforeach
            </div>
        @endif
        @if ($results['categories']->count() > 0)
            <h2 class="text-lg font-bold mb-2">Категории</h2>
            <div class="grid gap-4 sm:grid-cols-1 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($results['categories'] as $category)
                    <a href="{{ route('client.catalog', $category->slug) }}" wire:navigate>
                        <button role="menuitem"
                            class="flex w-full cursor-pointer select-none items-center gap-3 rounded-lg px-3 pb-2 pt-[9px] text-start leading-tight outline-none transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">
                            <div class="flex items-center justify-center rounded-lg !bg-blue-gray-50 p-2 ">
                                <div class="w-6">
                                    {{ svg($category->icon) }}
                                </div>
                            </div>
                            <div>
                                <h6
                                    class="flex items-center font-sans text-sm font-bold tracking-normal text-blue-gray-900 antialiased">
                                    {{ $category->title }}
                                </h6>
                            </div>
                        </button>
                    </a>
                @endforeach
            </div>
        @endif
    @endif
</div>
