<section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">
    @if ($category || $items)
        <div class="mx-auto container relative">
            <!-- Loading Overlay -->
            <div wire:loading.flex wire:target="selectedCategories, selectedVariations, priceFrom, priceTo, updateSort" 
            class="fixed inset-0 z-[9999] items-center justify-center bg-black/20 backdrop-blur-sm">
                <div class="flex items-center gap-2 rounded-lg bg-white/80 px-6 py-4 shadow-lg dark:bg-gray-800/80">
                    <div class="animate-spin w-6 h-6 border-4 border-blue-600 border-t-transparent rounded-full"></div>
                    <span class="text-gray-700 dark:text-gray-300">Загрузка...</span>
                </div>
            </div>
            <!-- Heading & Filters -->
            @if ($category )
                <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                        <div>
                            @livewire('general.breadcrumbs')
                            <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">{{ $category->name }}</h2>
                        </div>
                    
                    <div class="flex items-center space-x-4 relative">
                        <button id="sortDropdownButton1" data-dropdown-toggle="dropdownSort1" type="button"
                            class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto"
                            wire:click="toggleSorting">
                            <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $this->getSortOptions()[$selectedSort]['icon'] }}" />
                            </svg>
                            {{ $this->getSortOptions()[$selectedSort]['label'] }}
                        </button>
                        <div class="absolute right-0 top-[calc(100%+10px)] z-50 w-48 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
                            wire:click.outside="closeSorting"
                            x-show="$wire.isSortingOpened"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            style="display: none;">
                            <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400"
                                aria-labelledby="sortDropdownButton">
                                @foreach ($this->getSortOptions() as $value => $option)
                                    <li>
                                        <button type="button" wire:click="updateSort('{{ $value }}')"
                                            @class([
                                                'inline-flex w-full items-center px-2 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white rounded-lg',
                                                'text-blue-600 dark:text-blue-500' => $selectedSort === $value,
                                                'text-gray-500 dark:text-gray-400' => $selectedSort !== $value,
                                            ])>
                                            <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $option['icon'] }}" />
                                            </svg>
                                            {{ $option['label'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <div
                        class="sticky top-10 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Фильтры</h3>
                            </div>

                            @if(!empty($selectedVariations) || $priceFrom !== null || $priceTo !== null)
                                <button
                                    wire:click="resetFilters"
                                    class="w-full mb-6 inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Сбросить фильтры
                                </button>
                            @endif
                            
                            <!-- Price Range Filter -->
                            <div class="mb-6">
                                <h4 class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Цена</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="price-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">От</label>
                                        <input
                                            type="number"
                                            id="price-from"
                                            wire:model.live="priceFrom"
                                            min="{{ $this->priceRange->min_price }}"
                                            max="{{ $this->priceTo }}"
                                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 mb-1"
                                            placeholder="От"
                                        >
                                        <input
                                            type="range"
                                            id="price-from-range"
                                            wire:model.live="priceFrom"
                                            min="{{ $this->priceRange->min_price }}"
                                            max="{{ $this->priceTo }}"
                                            class="block w-full rounded-lg bg-gray-50 p-0.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                        >
                                        

                                    </div>
                                    <div>
                                        <label for="price-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">До</label>
                                        <input
                                            type="number"
                                            id="price-to"
                                            wire:model.live="priceTo"
                                            min="{{ $this->priceFrom }}"
                                            max="{{ $this->priceRange->max_price }}"
                                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 mb-1"
                                            placeholder="До"
                                        >
                                        <input
                                            type="range"
                                            id="price-to-range"
                                            wire:model.live="priceTo"
                                            min="{{ $this->priceFrom }}"
                                            max="{{ $this->priceRange->max_price }}"
                                            class="block w-full rounded-lg bg-gray-50 p-0.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                        >
                                    </div>
                                </div>
                            </div>
                            @foreach ($this->availableFilters as $param)
                                <div class="mb-4">
                                    <h4 class="mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $param->name }}
                                    </h4>
                                    
                                    @if($param->type === 'color')
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($param->params as $item)
                                                <label for="param-{{ $item->id }}" 
                                                    @if(!$item->would_have_results && !in_array($item->id, $selectedVariations))
                                                        class="relative cursor-not-allowed"
                                                    @else
                                                        class="relative cursor-pointer"
                                                    @endif>
                                                    <input type="checkbox"
                                                        id="param-{{ $item->id }}"
                                                        value="{{ $item->id }}"
                                                        wire:model.live="selectedVariations"
                                                        @if(!$item->would_have_results && !in_array($item->id, $selectedVariations)) disabled @endif
                                                        class="sr-only peer">
                                                    <div class="w-8 h-8 rounded-full border-2 peer-checked:border-blue-500"
                                                        style="background-color: {{ $item->value }}; @if(!$item->would_have_results && !in_array($item->id, $selectedVariations)) opacity: 0.3; filter: grayscale(70%); @endif"
                                                        title="{{ $item->title }}">
                                                    </div>
                                                    <div class="absolute inset-0 rounded-full peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:ring-offset-2"></div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                            @foreach ($param->params as $item)
                                                <div class="flex items-center">
                                                    <input type="checkbox"
                                                        id="param-{{ $item->id }}"
                                                        value="{{ $item->id }}"
                                                        wire:model.live="selectedVariations"
                                                        @if(!$item->would_have_results && !in_array($item->id, $selectedVariations)) disabled @endif
                                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 @if(!$item->would_have_results && !in_array($item->id, $selectedVariations)) opacity-50 cursor-not-allowed @endif">
                                                    <label for="param-{{ $item->id }}"
                                                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 @if(!$item->would_have_results && !in_array($item->id, $selectedVariations)) opacity-50 @endif">
                                                        {{ $item->title }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-4 col-span-3">
                    @if($this->products->isEmpty())
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
                        <div class="mb-4 grid gap-4 sm:grid-cols-1 md:mb-8 lg:grid-cols-2 xl:grid-cols-3">
                            @foreach ($this->products as $product)
                                @livewire('general.product', [
                                    'product' => $product,
                                ], key($product->id))
                            @endforeach
                        </div>
                    @endif
                    @if ($category) 
                        {{ $this->products->links() }}
                    @endif
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
