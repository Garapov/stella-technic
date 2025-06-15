<div>

    <div class="rounded-lg bg-blue-500 flex items-center relative w-full z-20 mb-4">
        <input type="search" id="search-dropdown"
        class="rounded-lg bg-white block p-2.5 w-full text-sm text-gray-900 dark:placeholder-gray-400 dark:text-white border border-blue-500"
        placeholder="Поиск" name="q" wire:model.live="q" @focus="isOpen = true" @blur="isOpen = false" style="outline: none; box-shadow: none;" />
        <button type="submit"
            class="rounded-e-lg py-2.5 px-4 text-sm font-medium h-full text-white bg-blue-500 border border-0">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
            <span class="sr-only">Поиск</span>
        </button>
    </div>

    @if ($q != '' && ($results['products']->count() > 0 || $results['categories']->count() > 0))
        @if ($results['products']->count() > 0)
            <h2 class="text-lg font-bold mb-2">Товары</h2>
            <div class="grid gap-4 sm:grid-cols-1 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($results['products'] as $product)
                    @livewire(
                        'general.product-variant',
                        [
                            'variant' => $product,
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
