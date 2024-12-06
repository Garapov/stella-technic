<section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">
    @if ($category)
        {{-- {{ $category->products }} --}}
        <div class="mx-auto container">
            <!-- Heading & Filters -->
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
                                d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4" />
                        </svg>
                        Сортировка
                        @if (!$isSortingOpened)
                        <x-fas-arrow-down class="w-2.5 h-2.5 ms-2.5" wire:loading.remove wire:target="toggleSorting" />
                        @else
                        <x-fas-arrow-up class="w-2.5 h-2.5 ms-2.5" wire:loading.remove wire:target="toggleSorting" />
                        @endif

                        <svg aria-hidden="true"
                            class="inline w-2.5 h-2.5 ms-2.5 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg" wire:loading
                            wire:target="toggleSorting">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                    </button>
                    @if ($isSortingOpened)
                    <div class="absolute right-0 top-[calc(100%+10px)] z-50 w-40 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
                        wire:click.outside="closeSorting">
                        <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400"
                            aria-labelledby="sortDropdownButton">
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    The most popular </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Newest </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Increasing price </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Decreasing price </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    No. reviews </a>
                            </li>
                            <li>
                                <a href="#"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Discount % </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
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
                                            max="{{ $this->priceRange->max_price }}"
                                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                            placeholder="От"
                                        >
                                    </div>
                                    <div>
                                        <label for="price-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">До</label>
                                        <input
                                            type="number"
                                            id="price-to"
                                            wire:model.live="priceTo"
                                            min="{{ $this->priceRange->min_price }}"
                                            max="{{ $this->priceRange->max_price }}"
                                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                            placeholder="До"
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
                    <div class="mb-4 grid gap-4 sm:grid-cols-1 md:mb-8 lg:grid-cols-2 xl:grid-cols-3">
                        @foreach ($this->products as $product)
                            @livewire('general.product', [
                                'product' => $product
                            ], key($product->id))
                        @endforeach
                    </div>
                    <div class="flex justify-center">
                        <nav aria-label="Page navigation example">
                            <ul class="flex items-center -space-x-px h-8 text-sm">
                                <li>
                                    <a href="#"
                                        class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                        <span class="sr-only">Previous</span>
                                        <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M5 1 1 5l4 4"></path>
                                        </svg>
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</a>
                                </li>
                                <li>
                                    <a href="#" aria-current="page"
                                        class="z-10 flex items-center justify-center px-3 h-8 leading-tight text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">3</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">4</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">5</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                        <span class="sr-only">Next</span>
                                        <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 9 4-4-4-4"></path>
                                        </svg>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('filter-reset', () => {
            const url = new URL(window.location.href);
            const path = url.pathname;
            console.log(path);
            
            window.history.pushState({}, '', path);
        });
    });
</script>
