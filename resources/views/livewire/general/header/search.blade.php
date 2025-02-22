<form action="{{ route('client.search') }}" class="relative w-full" x-data="{ isOpen: false }">
    <input type="search" id="search-dropdown"
        class="block p-2.5 w-full z-20 text-sm text-gray-900 dark:placeholder-gray-400 dark:text-white border-none"
        placeholder="Поиск" name="q" wire:model.live="q" @focus="isOpen = true" @blur="isOpen = false" />
    <button type="submit"
        class="absolute rounded-e-lg top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
        </svg>
        <span class="sr-only">Поиск</span>
    </button>
    @if ($q != '' && $results['products']->count() > 0)
        <div class="absolute top-full left-0 right-0 bg-gray-50 dark:bg-gray-700 border-s-gray-50 dark:border-s-gray-700 p-4 z-40 h-[400px] overflow-y-scroll"
            x-show="isOpen">
            @if ($results['products']->count() > 0)
                <h2 class="text-lg font-bold mb-2">Товары</h2>
                <ul>
                    @foreach($results['products'] as $product)
                        <li>
                            <a href="{{ route('client.product_detail', $product->slug) }}" wire:navigate>{{ $product->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($results['categories']->count() > 0)
                <h2 class="text-lg font-bold mb-2">Категории</h2>
                <ul>
                    @foreach($results['categories'] as $category)
                        <li>
                            <a href="{{ route('client.catalog', $category->slug) }}" wire:navigate>{{ $category->title }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</form>
