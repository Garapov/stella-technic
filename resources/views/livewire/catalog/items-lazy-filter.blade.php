<div class="rounded-lg bg-slate-50 p-3 shadow-sm flex flex-col gap-4 overflow-y-auto fixed md:static left-0 top-0 bottom-0 right-0 z-50 md:translate-x-0 transition-all"
    :class="isFilterOpened ? 'translate-x-0' : '-translate-x-full'" x-cloak>
    <div class="flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Фильтры</h3>
            <button class="navbar-close block md:hidden" @click="isFilterOpened = false">
                <svg class="h-6 w-6 text-gray-400 cursor-pointer hover:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <x-catalog.filter.price :variations="$this->variations" />

        @foreach($this->paramGroups as $paramName => $paramGroup)
            @if ($paramGroup[0]->productParam->type == 'slider')
                <x-catalog.filter.slider :paramName="$paramName" :paramGroup="$paramGroup"
                    :availableParams="$this->availableParams" />
                {{-- <x-catalog.filter.checkboxes :paramName="$paramName" :paramGroup="$paramGroup"
                    :availableParams="$this->availableParams" /> --}}
            @else
                <x-catalog.filter.checkboxes :paramName="$paramName" :paramGroup="$paramGroup"
                    :availableParams="$this->availableParams" :filters="$this->filters" />
            @endif
        @endforeach

        <button wire:click="resetFilters"
            class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Сбросить фильтры
        </button>
    </div>
</div>