<section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">
    @if ($category || $product_ids)
        <div class="mx-auto container relative">
            <!-- Loading Overlay -->
            <div wire:loading.class.remove="hidden"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm hidden">
                <div class="flex items-center gap-2 rounded-lg bg-white/80 px-6 py-4 shadow-lg dark:bg-gray-800/80">
                    <div class="animate-spin w-6 h-6 border-4 border-blue-600 border-t-transparent rounded-full"></div>
                    <span class="text-gray-700 dark:text-gray-300">Загрузка...</span>
                </div>
            </div>
            <!-- Heading & Filters -->
            @if ($category)
                <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                    <div>
                        @livewire('general.breadcrumbs')
                        <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">{{ $category->name }}</h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Display Mode Toggle -->
                        <div class="inline-flex rounded-md shadow-xs" role="group">
                            <div class="inline-flex items-center px-3 py-2 text-sm font-medium border rounded-s-lg @if ($mode === 'list') bg-blue-500 border-blue-500 text-white @else bg-white border-gray-200 text-gray-900 cursor-pointer @endif" 
                                wire:click="changeDisplayMode('list')">
                                <x-carbon-horizontal-view class="w-4 h-4" />
                            </div>
                            <div class="inline-flex items-center px-3 py-2 text-sm font-medium border rounded-e-lg @if ($mode === 'block') bg-blue-500 border-blue-500 text-white @else bg-white border-gray-200 text-gray-900 cursor-pointer @endif" 
                                wire:click="changeDisplayMode('block')" >
                                <x-carbon-vertical-view class="w-4 h-4" />
                            </div>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="relative" x-data="{sortingOpened: false}">
                            <button type="button" class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto"
                                @click="sortingOpened = true;">
                                <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->getSortOptions()[$sort]['icon'] }}" />
                                </svg>
                                {{ $this->getSortOptions()[$sort]['label'] }}
                            </button>

                            <!-- Sort Options Dropdown -->
                            <div class="absolute right-0 top-[calc(100%+10px)] z-50 w-48 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
                                @click.outside="sortingOpened = false"
                                x-show="sortingOpened"
                                style="display: none;">
                                <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                                    @foreach ($this->getSortOptions() as $value => $option)
                                        <li>
                                            <button type="button" @click="$wire.updateSort('{{ $value }}'); sortingOpened = false"
                                                @class([
                                                    'inline-flex w-full items-center px-2 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white rounded-lg',
                                                    'text-blue-600 dark:text-blue-500' => $sort === $value,
                                                    'text-gray-500 dark:text-gray-400' => $sort !== $value,
                                                ])>
                                                <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $option['icon'] }}" />
                                                </svg>
                                                {{ $option['label'] }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $all_products = $products->get();
                $paginated_products = $products->paginate(12);
            @endphp
            <div class="grid grid-cols-6 gap-4">
                @if ($display_filter)
                    <div>
                        @livewire('catalog.filter', [
                            'products' => $all_products,
                        ])
                        {{ print_r($filters) }}
                    </div>
                @endif
                <div class="flex flex-col gap-4 @if ($display_filter) col-span-5 @else col-span-full @endif">
                    @if($paginated_products->isEmpty())
                        <div class="flex flex-col items-center justify-center p-8 text-center">
                            <div class="mb-4">
                                <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Ничего не найдено</h3>
                            <p class="text-gray-500 dark:text-gray-400">По выбранным фильтрам товары не найдены. Попробуйте изменить параметры поиска.</p>
                            <button wire:click="resetFilters" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Сбросить фильтры
                            </button>
                        </div>
                    @else
                        <div>
                            <div class="mb-4 grid gap-4 sm:grid-cols-1 md:mb-8 @if ($display_filter) lg:grid-cols-2 xl:grid-cols-4 @else lg:grid-cols-3 xl:grid-cols-5 @endif">
                                @foreach ($paginated_products as $variant)
                                    @livewire('general.product-variant', [
                                        'variant' => $variant,
                                    ], key('variant_' . $variant->id))
                                @endforeach
                            </div>
                            
                        </div>
                    @endif
                    {{ $paginated_products->links() }}

                </div>
            </div>
        </div>
    @endif
</section>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('filter-reset', () => {
            setTimeout(() => {
                const url = new URL(window.location.href);
                const path = url.pathname;
                console.log(path);

                window.history.pushState({}, '', path);
            }, 0);
        });
    });
</script>
