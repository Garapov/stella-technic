<section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased"
    x-data="{
        isLoading: false,
        hasError: false,
        errorMessage: '',
        displayMode: $wire.entangle('displayMode'),
        init() {
            Livewire.on('filter-changed', () => {
                this.isLoading = true;
                this.hasError = false;
                setTimeout(() => {
                    this.isLoading = false;
                }, 500);
            });

            Livewire.on('filter-error', (message) => {
                this.hasError = true;
                this.errorMessage = message || 'Произошла ошибка при обработке фильтра';
                this.isLoading = false;
            });
        },
        makeFilterIsLoading() {
            this.isLoading = true;
        },
    }"
>
    @if ($category || $product_ids)
        <div class="mx-auto container relative">
            <!-- Loading Overlay -->
            <div x-show="isLoading" :class="{'hidden': !isLoading}"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm hidden">
                <div class="flex items-center gap-2 rounded-lg bg-white/80 px-6 py-4 shadow-lg dark:bg-gray-800/80">
                    <div class="animate-spin w-6 h-6 border-4 border-blue-600 border-t-transparent rounded-full"></div>
                    <span class="text-gray-700 dark:text-gray-300">Загрузка...</span>
                </div>
            </div>

            <!-- Error Message -->
            <div x-show="hasError" :class="{'hidden': !hasError}"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm hidden"
                x-on:click="hasError = false">
                <div class="flex flex-col items-center gap-2 rounded-lg bg-white/80 px-6 py-4 shadow-lg dark:bg-gray-800/80 max-w-md">
                    <div class="text-red-600 dark:text-red-400 text-xl mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Ошибка
                    </div>
                    <p class="text-gray-700 dark:text-gray-300" x-text="errorMessage"></p>
                    <button
                        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        x-on:click="hasError = false"
                    >
                        Закрыть
                    </button>
                </div>
            </div>
            <!-- Heading & Filters -->
            @if ($category)
                <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                    <div>
                        @livewire('general.breadcrumbs')
                        <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">{{ $category->name }}</h2>
                    </div>

                    <div class="flex items-center space-x-4 relative">
                        <!-- Display Mode Toggle -->
                        <div class="inline-flex rounded-md shadow-xs" role="group">
                            <div class="inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-gray-500 focus:bg-gray-900 focus:text-white dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:bg-gray-700" 
                                wire:click="changeDisplayMode('list')" 
                                :class="{'bg-gray-100 text-blue-700': displayMode == 'list', 'bg-white text-gray-900 cursor-pointer': displayMode != 'list'}">
                                <x-carbon-horizontal-view class="w-4 h-4" />
                            </div>
                            <div class="inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-gray-500 focus:bg-gray-900 focus:text-white dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:bg-gray-700" 
                                wire:click="changeDisplayMode('block')" 
                                :class="{'bg-gray-100 text-blue-700': displayMode == 'block', 'bg-white text-gray-900 cursor-pointer': displayMode != 'block'}">
                                <x-carbon-vertical-view class="w-4 h-4" />
                            </div>
                        </div>

                        <!-- Sort Dropdown -->
                        <button type="button" class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto"
                            wire:click="toggleSorting">
                            <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->getSortOptions()[$selectedSort]['icon'] }}" />
                            </svg>
                            {{ $this->getSortOptions()[$selectedSort]['label'] }}
                        </button>

                        <!-- Sort Options Dropdown -->
                        <div class="absolute right-0 top-[calc(100%+10px)] z-50 w-48 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
                            wire:click.outside="closeSorting"
                            x-show="$wire.showSorting"
                            style="display: none;">
                            <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                                @foreach ($this->getSortOptions() as $value => $option)
                                    <li>
                                        <button type="button" wire:click="updateSort('{{ $value }}')"
                                            @class([
                                                'inline-flex w-full items-center px-2 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white rounded-lg',
                                                'text-blue-600 dark:text-blue-500' => $selectedSort === $value,
                                                'text-gray-500 dark:text-gray-400' => $selectedSort !== $value,
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
            @endif
            <div class="grid grid-cols-6 gap-4">
                @if ($display_filter && !$products->isEmpty())
                    <div>
                        фильтр переделывается
                        {{--@livewire('catalog.filter', [
                            'category' => $category,
                            'product_ids' => $product_ids,
                            'items' => $items
                        ])--}}
                    </div>
                @endif
                <div class="flex flex-col gap-4 @if ($display_filter && !$products->isEmpty()) col-span-5 @else col-span-full @endif">
                    @if($products->isEmpty())
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
                                @foreach ($products as $variant)
                                    @livewire('general.product-variant', [
                                        'variant' => $variant,
                                    ], key('variant_' . $variant->id))
                                @endforeach
                            </div>
                            
                        </div>
                    @endif
                    {{ $products->links() }}

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
