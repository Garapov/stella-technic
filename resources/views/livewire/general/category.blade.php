<a href="{{ route('client.catalog', $category->slug) }}" wire:navigate class="glide__slide p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 relative overflow-hidden h-auto flex flex-col justify-between gap-8">
    <div class="w-[45%] absolute top-[-10px] right-[-10px] opacity-10 text-yellow-900 dark:opacity-20">
        {{ svg($category->icon) }}
    </div>
    <div class="flex flex-col items-start gap-2">
        <span class="bg-yellow-100 text-yellow-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">
            {{ count($category->products) . ' ' . (count($category->products) % 10 === 1 && count($category->products) % 100 !== 11 ? 'товар' : (count($category->products) % 10 >= 2 && count($category->products) % 10 <= 4 && (count($category->products) % 100 < 10 || count($category->products) % 100 >= 20) ? 'товара' : 'товаров')) }}
        </span>
        <h5 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $category->title }}</h5>
        @if ($category->description)
            <!-- <p class="text-gray-500 dark:text-gray-400 max-w-[50%]">{{ $category->description }}</p> -->
        @endif
    </div>
    <div class="flex items-center justify-between">
        <span class="inline-flex items-center text-yellow-800 hover:underline dark:text-yellow-300">
            Все товары
            <x-carbon-arrow-up-right class="w-3 h-3 ms-2.5 rtl:rotate-[270deg]" />
        </span>
        <div class="text-lg font-bold text-gray-900 dark:text-white">От {{ $category->minProductPrice() }} ₽</div>
    </div>
</a>

