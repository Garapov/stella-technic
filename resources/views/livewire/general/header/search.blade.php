<form action="{{ route('client.search') }}" class="relative flex items-center grow" x-data="{ isOpen: false }">
    <div class="rounded-lg bg-blue-500 flex items-center relative w-full z-20">
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
    @if ($q != '' && $results['products']->count() > 0)
        <div class="absolute top-full left-0 right-0 bg-white border border-blue-500 pt-8 pb-4 px-4 h-[400px] overflow-y-scroll rounded-lg -mt-4 z-10"
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
