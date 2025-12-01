<div>

    {{-- <div class="rounded-lg bg-blue-500 flex items-center w-full mb-4">
        <input type="search" id="search-dropdown"
            class="rounded-lg bg-white block p-2.5 w-full text-sm text-gray-900 dark:placeholder-gray-400 dark:text-white border border-blue-500"
            placeholder="Поиск" name="q" wire:model.live="q" @focus="isOpen = true" @blur="isOpen = false"
            style="outline: none; box-shadow: none;" />
        <button type="submit"
            class="rounded-e-lg py-2.5 px-4 text-sm font-medium h-full text-white bg-blue-500 border border-0">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
            <span class="sr-only">Поиск</span>
        </button>
    </div> --}}
    @if ($q != '' && ($results['products']->count() > 0 || $results['categories']->count() > 0))
        <div class="p-4 mb-4 text-md text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
            role="alert">
            Результат поиска по запросу <span class="font-medium">"{{ $q }}"</span>. Найдено
            {{ $results['products']->count() }} товаров и {{ $results['categories']->count() }} категорий.
        </div>
    @endif
    @if ($q != '' && ($results['products']->count() < 1 && $results['categories']->count() < 1))
        <div class="p-4 mb-4 text-md text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
            role="alert">
            По запросу <span class="font-medium">"{{ $q }}"</span> ничего не найдено.
        </div>
    @endif
    @if ($q == '')
        <div class="p-4 text-md text-gray-800 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300" role="alert">
            Для показа результатов введите запрос в поле поиска выше.
        </div>
    @endif
    @if ($q != '' && ($results['products']->count() > 0 || $results['categories']->count() > 0))
        @if ($results['categories']->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-4">
                @foreach ($results['categories'] as $subcategory)

                    <x-catalog.category.big :subcategory="$subcategory" />
                @endforeach
            </div>
            {{-- <h2 class="text-lg font-bold mb-2">Категории</h2>
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
            </div> --}}
        @endif
        @if ($results['products']->count() > 0)
            {{-- <h2 class="text-lg font-bold mb-2">Товары</h2>
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
            </div> --}}
            @livewire('catalog.items', ['products' => $results['products']->pluck('id'), 'display_filter' => false, 'inset' => true,])
        @endif

    @endif
</div>