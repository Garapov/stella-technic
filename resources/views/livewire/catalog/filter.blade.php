<div class="rounded-lg bg-slate-50 p-3 shadow-sm flex flex-col gap-4 overflow-y-auto fixed md:static left-0 top-0 bottom-0 right-0 z-50 md:translate-x-0 transition-all" :class="isFilterOpened ? 'translate-x-0' : '-translate-x-full'" x-data="{
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
}" x-cloak>

    <div class="flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Фильтры</h3>
            <button class="navbar-close block md:hidden" @click="isFilterOpened = false">
                <svg class="h-6 w-6 text-gray-400 cursor-pointer hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <h4 class="text-md font-semibold mb-3 dark:text-white">Цена</h4>

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
            <div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 flex flex-col gap-3" x-data="{
                isOpened: true
            }">
                {{-- {{ $params }} --}}
                @if ($params->first()['type'] != 'switch')
                    <div class="flex items-center justify-between cursor-pointer" @click="isOpened = !isOpened">
                        <span class="text-[0.9rem] font-semibold dark:text-white">{{ $paramName }}</span>
                        <span class="block min-w-6 max-w-6 min-h-6 max-h-6" :class="{'rotate-180': isOpened}">
                            <x-eva-arrow-ios-downward-outline class="w-full h-full"  />
                        </span>
                    </div>
                @endif

                @if ($params->first()['type'] == 'color')
                    <ul class="flex flex-wrap gap-2" aria-labelledby="dropdownDefault" x-show="isOpened">
                        @foreach($params as $colorItemId => $color)
                            @php $colors = explode('|', $color['value']); @endphp
                            <label class="relative w-8 h-8 rounded-full border 
                                @if(isset($selectedParams[$colorItemId])) border-blue-800 border-4 
                                @else border-gray-300 
                                @endif overflow-hidden
                                @unless(in_array($colorItemId, $availableFilters)) opacity-30 cursor-not-allowed pointer-events-none @endunless">
                                <input
                                    type="checkbox"
                                    id="param_{{ $colorItemId }}"
                                    class="hidden"
                                    wire:click="toggleParam({{ $colorItemId }}, '{{ $color['source'] }}')"
                                    @if(isset($selectedParams[$colorItemId])) checked @endif
                                    @unless(in_array($colorItemId, $availableFilters)) disabled @endunless
                                />
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
                @elseif ($params->first()['type'] == 'slider')
                    
                    @php
                        // $values = [0, 25, 50, 100, 200, 500];
                        $ar_values = $params->sortBy('value')->map(function ($item) use($availableFilters) {
                            return intval($item['value']);
                        })->toArray();
                        // dd($ar_values);  
                        $values = [];
                        foreach ($ar_values as $value) {
                            $values[] = $value;
                        }
                        $alpineKey = Str::snake(Str::of($paramName)->transliterate()->toString()); // уникальный ключ для Alpine
                        $valuesHash = md5(json_encode($values));

                        $items = $params->sortBy('value')->toArray();
                        $selected_value_min = 0;
                        $selected_value_max = count($values) - 1;

                        if (isset($filters['$includes']) && isset($filters['$includes'][$alpineKey]) && count($filters['$includes'][$alpineKey]) > 0) {
                            $selected_value_min = array_search($items[$filters['$includes'][$alpineKey][0]]['value'], $values) ?? 0;
                            $selected_value_max = array_search($items[$filters['$includes'][$alpineKey][count($filters['$includes'][$alpineKey]) - 1]]['value'], $values) ?? count($values) - 1;
                        }
                    @endphp

                    {{-- {{ dd($values) }} --}}
                    <div key="{{ $alpineKey . '_' . $valuesHash }}" wire:ignore x-data="{
                        values: @json($values),
                        minIndex: {{$selected_value_min}},
                        maxIndex: {{$selected_value_max}},
                        dragThumb: null,
                        trackRect: null,
                        minValue: {{ $values[$selected_value_min] }},
                        maxValue: {{ $values[$selected_value_max] }},
                        init() {
                            console.log(this.values);
                        },
                        setMinValue(event) {
                            if (this.values[this.findClosestIndex(event.target.value)] > this.values[this.maxIndex]) {
                                this.minValue = this.values[this.maxIndex];
                                this.minIndex = this.maxIndex;
                            } else {
                                this.minValue = this.values[this.findClosestIndex(event.target.value)];
                                this.minIndex = this.findClosestIndex(event.target.value);
                            }
                            this.dragThumb = 'min';
                            {{-- console.log(this.minIndex, this.minValue); --}}
                            event.target.blur();
                            this.setFilter();
                            {{-- this.setFilter(); --}}
                        },
                        setMaxValue(event) {
                            if (this.values[this.findClosestIndex(event.target.value)] < this.values[this.minIndex]) {
                                this.maxValue = this.values[this.minIndex];
                                this.maxIndex = this.minIndex;
                            } else {
                                this.maxValue = this.values[this.findClosestIndex(event.target.value)];
                                this.maxIndex = this.findClosestIndex(event.target.value);
                            }
                            this.dragThumb = 'max';
                            {{-- console.log(this.maxIndex, this.maxValue); --}}
                            event.target.blur();
                            this.setFilter();
                            {{-- this.setFilter(); --}}
                        },
                        findClosestIndex(value) {
                            let closestIndex = this.values.reduce((prevIndex, curr, index) => {
                                return Math.abs(curr - value) < Math.abs(this.values[prevIndex] - value) ? index : prevIndex;
                            }, 0);

                            return closestIndex;
                        },
                        get minThumbStyle() {
                            return {
                                left: `${((this.minIndex / (this.values.length - 1)) * 100) * this.$refs.track.getBoundingClientRect().width / 100}px`,
                                transform: `translate(-${(this.minIndex / (this.values.length - 1)) * 100}%, 0)`
                            };
                        },

                        get maxThumbStyle() {
                            return {
                                right: `${this.$refs.track.getBoundingClientRect().width - (((this.maxIndex / (this.values.length - 1)) * 100) * this.$refs.track.getBoundingClientRect().width / 100)}px`,
                                transform: `translate(${100 - ((this.maxIndex / (this.values.length - 1)) * 100)}%, 0)`
                            };
                        },

                        get activeRangeStyle() {
                            const left = (this.minIndex / (this.values.length - 1)) * 100;
                            const width = ((this.maxIndex - this.minIndex) / (this.values.length - 1)) * 100;
                            return {
                                left: `${left}%`,
                                width: `${width}%`
                            };
                        },

                        setFilter() {
                            {{-- this.onChange(); --}}
                            $wire.setSliderFilter({{ json_encode($params->sortBy('value')->toArray()) }}, [this.values[this.minIndex], this.values[this.maxIndex]], '{{ $paramName }}');
                        },

                        startDrag(e) {
                            e.preventDefault();
                            this.trackRect = this.$refs.track.getBoundingClientRect();

                            const moveHandler = (event) => {
                                let clientX = event.type.includes('touch') ? event.touches[0].clientX : event.clientX;
                                const percent = (clientX - this.trackRect.left) / this.trackRect.width;
                                const index = Math.round(percent * (this.values.length - 1));

                                if (this.dragThumb === 'min') {
                                    this.minIndex = Math.min(Math.max(0, index), this.maxIndex);

                                    this.minValue = this.values[this.minIndex];
                                } else if (this.dragThumb === 'max') {
                                    this.maxIndex = Math.max(Math.min(this.values.length - 1, index), this.minIndex);
                                    this.maxValue = this.values[this.maxIndex];
                                }
                            };

                            const upHandler = () => {
                                this.dragThumb = null;
                                window.removeEventListener('mousemove', moveHandler);
                                window.removeEventListener('mouseup', upHandler);
                                window.removeEventListener('touchmove', moveHandler);
                                window.removeEventListener('touchend', upHandler);

                                this.setFilter();  

                                
                            };

                            window.addEventListener('mousemove', moveHandler);
                            window.addEventListener('mouseup', upHandler);
                            window.addEventListener('touchmove', moveHandler);
                            window.addEventListener('touchend', upHandler);
                        }
                    }" class="relative w-full max-w-xl mx-auto" x-show="isOpened">

                        <div class="flex items-center gap-4 mb-3">
                            <input type="number" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="От" @blur="setMinValue" x-model="minValue">
                            <input type="number" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="До" @blur="setMaxValue" x-model="maxValue">
                        </div>

                        <!-- Трек слайдера -->
                        <div
                            x-ref="track"
                            class="relative h-2 bg-gray-200 rounded"
                            @mousedown="startDrag($event)"
                            @touchstart="startDrag($event)"
                        >
                            <!-- Активный диапазон -->
                            <div
                                class="absolute h-2 bg-blue-500 rounded"
                                :style="activeRangeStyle"
                            ></div>

                            <!-- Левая ручка -->
                            <div
                                class="absolute w-5 h-5 bg-white border-2 border-blue-700 rounded-full cursor-pointer -top-1.5 z-10"
                                :style="minThumbStyle"
                                @mousedown="dragThumb = 'min'"
                                @touchstart="dragThumb = 'min'"
                            ></div>

                            <!-- Правая ручка -->
                            <div
                                class="absolute w-5 h-5 bg-white border-2 border-blue-700 rounded-full cursor-pointer -top-1.5 z-10"
                                :style="maxThumbStyle"
                                @mousedown="dragThumb = 'max'"
                                @touchstart="dragThumb = 'max'"
                            ></div>
                        </div>

                        <!-- Метки -->
                        {{-- <div class="flex items-center justify-between mt-3 h-4">
                            <div
                                class="text-xs"
                                class="text-blue-700"
                                x-text="minIndex !== null ? values[minIndex] : ''"
                            ></div>
                            <div
                                class="text-xs"
                                class="text-blue-700"
                                x-text="maxIndex !== null ? values[maxIndex] : ''"
                            ></div>
                        </div> --}}
                    </div>
                    
                @elseif ($params->first()['type'] == 'switch')
                    <label class="inline-flex items-center justify-between cursor-pointer @unless(in_array($params->first()['id'], $availableFilters)) opacity-30 cursor-not-allowed pointer-events-none @endunless">
                        <span class="me-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $paramName }}</span>
                        <input type="checkbox" id="param_{{ $params->first()['id']}}" wire:click="toggleParam({{ $params->first()['id'] }}, '{{ $params->first()['source'] }}')" class="sr-only peer" @unless(in_array($params->first()['id'], $availableFilters)) disabled @endunless>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600"></div>
                    </label>
                @else
                    <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault" x-data="{ showAll: false }" x-show="isOpened">
                        @php
                            $counter = 0;
                        @endphp
                        @foreach($params->sortBy('sort') as $paramItemId => $paramData)
                            <li class="flex items-center @unless(in_array($paramItemId, $availableFilters)) opacity-30 cursor-not-allowed pointer-events-none @endunless"
                                @if($counter > 4 && !isset($selectedParams[$paramItemId])) x-show="showAll" @endif>
                                <input
                                    type="checkbox"
                                    id="param_{{ $paramItemId }}"
                                    class="w-5 h-5 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                    wire:click="toggleParam({{ $paramItemId }}, '{{ $paramData['source'] }}')"
                                    {{ isset($selectedParams[$paramItemId]) ? 'checked' : '' }}
                                    @unless(in_array($paramItemId, $availableFilters)) disabled @endunless
                                />
                                <label for="param_{{ $paramItemId }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $paramData['title'] }}
                                </label>
                            </li>
                            @php $counter++; @endphp
                        @endforeach

                        @if (count($params) > 5)
                            <li @click="showAll = !showAll" class="cursor-pointer font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                Показать <template x-if="!showAll"><span>больше</span></template><template x-if="showAll"><span>меньше</span></template>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
        @endforeach

        @if ($brands)
            <div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 flex flex-col gap-3" x-data="{
                isOpened: false
            }">
                <div class="flex items-center justify-between cursor-pointer" @click="isOpened = !isOpened">
                    <span class="text-[0.9rem] font-semibold dark:text-white">Бренд</span>
                    <span class="block min-w-6 max-w-6 min-h-6 max-h-6" :class="{'rotate-180': isOpened}">
                        <x-eva-arrow-ios-downward-outline class="w-full h-full"  />
                    </span>
                </div>
                <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault" x-show="isOpened">
                    @foreach ($brands as $brand)
                        <li class="flex items-center">
                            <input type="checkbox"
                                id="brand_{{ $brand->id }}"
                                wire:model.live="selectedBrands"
                                value="{{ $brand->id }}"
                            class="w-5 h-5 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

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
                            class="w-5 h-5 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />

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
        <button type="button" class="px-4 py-2.5 text-sm font-medium text-white inline-flex items-center bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-300" @click="resetFilters">
            <x-carbon-close-outline class="w-4 h-4 text-white me-2" />
            Сбросить фильтры
        </button>
    @endif
</div>
