<section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased"
    x-data="{
        init() {
            $wire.loadFavorites($store.favorites.list);
        }
    }"
>
    <div class="mx-auto container">
        <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
            <div>
                @livewire('general.breadcrumbs')
                <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Избранное</h2>
            </div>
        </div>

        @if ($isLoading)
            <div class="animate-pulse">
                <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
                    <div class="h-64 bg-gray-200 rounded mb-4"></div>
                    <div class="h-64 bg-gray-200 rounded mb-4"></div>
                    <div class="h-64 bg-gray-200 rounded mb-4"></div>
                    <div class="h-64 bg-gray-200 rounded mb-4"></div>
                </div>
            </div>
        @else

            @if($products->isEmpty())
                <div class="text-center py-12">
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Нет избранных товаров</h3>
                    <p class="mt-1 text-sm text-gray-500">Начните добавлять товары в избранное.</p>
                    <div class="mt-6">
                        <a href="{{ route('client.catalog.all') }}"
                            class="inline-flex items-center rounded-md bg-blue-500 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                            Перейти в каталог
                        </a>
                    </div>
                </div>
            @else
                <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($products as $product)
                        @livewire('general.product-variant', ['variant' => $product], key('product-'.$product->id))
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</section>
