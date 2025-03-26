<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Фильтры</h3>
        </div>

        @if(!empty($selectedVariations))
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
                <!-- Price inputs -->
                <!-- ... (код из оригинального шаблона) ... -->
            </div>
        </div>

        <!-- Filters -->
        @foreach ($filters as $filter)
            @if(!empty($filter['items']) && count($filter['items']) > 0)
                <div class="mb-4">
                    <h4 class="mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ $filter['name'] }}
                    </h4>
                    @if($filter['type'] === 'param')
                        <div class="@if($filter['name'] === 'Цвет') flex flex-wrap gap-2 @else space-y-2 @endif">
                            @foreach($filter['items'] as $item)
                                <!-- ... parameter items ... -->
                            @endforeach
                        </div>
                    @elseif($filter['type'] === 'brand')
                        <div class="space-y-2">
                            @foreach($filter['items'] as $brand)
                                <!-- ... brand items ... -->
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</div> 