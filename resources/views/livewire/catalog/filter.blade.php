<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900 flex flex-col gap-4" x-data="{
    rangeSlider: window.rangeSlider($refs.range, {
        min: @js($products->min('price')),
        max: @js($products->max('price')),
        value: @js($startPriceRange),
        step: 1,
        onInput: function (value) {
            this.value = value;
            $wire.set('priceRangeToDisplay', value);
        },
        onThumbDragEnd: function () {
            $wire.set('priceRange', this.value);
        },
        onRangeDragEnd: function () {
            $wire.set('priceRange', this.value);
        }
    }),
    resetFilters() {
        $wire.resetFilters();
        this.rangeSlider.value = [@js($products->min('price')), @js($products->max('price'))];
    }
}">
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Фильтры</h3>
        </div>

        <div>
            <h4 class="text-md font-medium mb-3 dark:text-white">Цена</h4>

            <div class="relative mt-4">
                <div class="relative w-full h-2 bg-gray-200 rounded-md">
                    <div x-ref="range" wire:ignore></div>
                </div>
            </div>

            <div class="flex justify-between mt-3 text-gray-600 dark:text-white">
                <span>{{ $priceRangeToDisplay[0] }} ₽</span>
                <span>{{ $priceRangeToDisplay[1] }} ₽</span>
            </div>
        </div>

        @foreach($parameters as $paramName => $params)
            <div>
                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    {{ $paramName }}
                </h6>
                {{--@if ($params->first()['type'] == 'color')--}}
                @if (false)
                    <ul class="flex flex-wrap gap-2" aria-labelledby="dropdownDefault">
                        @foreach($params as $colorItemId => $color)
                            @php
                                $colors = explode('|', $color['value']);
                            @endphp
                            <label class="relative w-5 h-5 rounded-full border @if(in_array($colorItemId, $selectedParams)) border-blue-800 border-4 @else border-gray-300 @endif overflow-hidden">
                                <input type="checkbox" id="param_{{ $colorItemId }}" wire:model.live="selectedParams" value="{{ $colorItemId }}" class="hidden" />
                                @if(count($colors) > 1)
                                    <div class="absolute inset-0">
                                        <div class="h-full w-1/2 float-left" style="background-color: {{ trim($colors[0]) }}"></div>
                                        <div class="h-full w-1/2 float-right" style="background-color: {{ trim($colors[1]) }}"></div>
                                    </div>
                                @else
                                    <div class="absolute inset-0" style="background-color: {{ trim($colors[0]) }}"></div>
                                @endif
                            </label>
                        @endforeach
                    </ul>
                @else
                    <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
                        @foreach($params as $paramItemId => $paramData)
                            <li class="flex items-center">
                                <input type="checkbox" id="param_{{ $paramItemId }}" wire:model.live="selectedParams" value="{{ $paramItemId }}"
                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                                <label for="param_{{ $paramItemId }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $paramData['title'] }}
                                </label>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
        @if ($brands)
            <div>
                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Бренд
                </h6>
                <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
                    @foreach ($brands as $brand)
                        <li class="flex items-center">
                            <input type="checkbox"
                                id="brand_{{ $brand->id }}"
                                wire:model.live="selectedBrands"
                                value="{{ $brand->id }}"
                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                            <label for="brand_{{ $brand->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $brand->name }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($batches)
            <div>
                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Серии
                </h6>
                <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
                    @foreach ($batches as $batch)
                        <li class="flex items-center">
                            <input type="checkbox"
                                id="batch_{{ $batch->id }}"
                                wire:model.live="selectedBatches"
                                value="{{ $batch->id }}"
                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

                            <label for="batch_{{ $batch->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $batch->name }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    @if (!empty($filters))
        <button type="button" class="px-4 py-2.5 text-sm font-medium text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @click="resetFilters">
            <x-carbon-close-outline class="w-4 h-4 text-white me-2" />
            Сбросить фильтры
        </button>
    @endif
</div>
