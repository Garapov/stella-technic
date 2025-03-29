<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900" x-data="{
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
}">
    <div>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Фильтры</h3>
        </div>

        <div class="mb-6">
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
            <div class="mb-6">
                <h4 class="text-md font-medium mb-3 dark:text-white">{{ $paramName }}</h4>
                <div class="space-y-2">
                    @foreach($params as $paramItemId => $paramData)
                        @if($paramData['type'] === 'color')
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="param_{{ $paramItemId }}"
                                    wire:model.live="selectedParams"
                                    value="{{ $paramItemId }}"
                                    class="mr-2"
                                >
                                <label for="param_{{ $paramItemId }}" class="flex items-center dark:text-white">
                                    {{ $paramData['title'] }}
                                    <span
                                        class="ml-2 w-4 h-4 inline-block rounded-full"
                                        style="background-color: {{ $paramData['value'] }}"
                                    ></span>
                                </label>
                            </div>
                        @else
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="param_{{ $paramItemId }}"
                                    wire:model.live="selectedParams"
                                    value="{{ $paramItemId }}"
                                    class="mr-2"
                                >
                                <label for="param_{{ $paramItemId }}" class="dark:text-white">
                                    {{ $paramData['title'] }}
                                </label>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
