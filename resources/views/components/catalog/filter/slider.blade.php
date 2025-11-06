{{-- {{ dd($values) }} --}}
<div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 flex flex-col gap-3" wire:loading.class="opacity-50 pointer-events-none" x-data="{
    isOpened: true
}">
    <div class="flex items-center justify-between cursor-pointer" @click="isOpened = !isOpened">
        <span class="text-[0.9rem] font-semibold dark:text-white">{{ $paramName }}</span>
        <span class="block min-w-6 max-w-6 min-h-6 max-h-6" :class="{'rotate-180': isOpened}">
            <x-eva-arrow-ios-downward-outline class="w-full h-full"  />
        </span>
    </div>
    @php
        // $values = [0, 25, 50, 100, 200, 500];
        $ar_values = $paramGroup->sortBy('value')->map(function ($item) use($availableParams) {
            return intval($item['value']);
        })->toArray(); 
        $values = [];
        foreach ($ar_values as $value) {
            $values[] = $value;
        }
        $alpineKey = Str::snake(Str::of($paramName)->transliterate()->toString()); // уникальный ключ для Alpine
        $valuesHash = md5(json_encode($values));

        $items = $paramGroup->sortBy('value')->toArray();
        $selected_value_min = 0;
        $selected_value_max = count($values) - 1;
        // dd([$paramGroup->whereIn('id', $this->filterParams['$includes'][$alpineKey])->min('value'), $paramGroup->whereIn('id', $this->filterParams['$includes'][$alpineKey])->max('value')]);
        if (isset($this->filterParams['$includes']) && isset($this->filterParams['$includes'][$alpineKey]) && count($this->filterParams['$includes'][$alpineKey]) > 0) {
            $selected_value_min = array_search($paramGroup->whereIn('id', $this->filterParams['$includes'][$alpineKey])->min('value'), $values) ?? 0;
            $selected_value_max = array_search($paramGroup->whereIn('id', $this->filterParams['$includes'][$alpineKey])->max('value'), $values) ?? count($values) - 1;

            // dd([$selected_value_min, $selected_value_max]);
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
                {{-- left: `${((this.minIndex / (this.values.length - 1)) * 100) * this.$refs.track.getBoundingClientRect().width / 100}px`, --}}
                transform: `translate(-${(this.minIndex / (this.values.length - 1)) * 100}%, 0)`
            };
        },

        get maxThumbStyle() {
            return {
                {{-- right: `${this.$refs.track.getBoundingClientRect().width - (((this.maxIndex / (this.values.length - 1)) * 100) * this.$refs.track.getBoundingClientRect().width / 100)}px`, --}}
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
            $wire.setSliderFilter({{ json_encode($paramGroup->sortBy('value')->toArray()) }}, [this.values[this.minIndex], this.values[this.maxIndex]], '{{ $paramName }}');
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
            >
                <div
                    class="absolute w-5 h-5 bg-white border-2 border-blue-700 rounded-full cursor-pointer -top-1.5 z-10 left-0"
                    :style="minThumbStyle"
                    @mousedown="dragThumb = 'min'"
                    @touchstart="dragThumb = 'min'"
                ></div>

                <!-- Правая ручка -->
                <div
                    class="absolute w-5 h-5 bg-white border-2 border-blue-700 rounded-full cursor-pointer -top-1.5 z-10 right-0"
                    :style="maxThumbStyle"
                    @mousedown="dragThumb = 'max'"
                    @touchstart="dragThumb = 'max'"
                ></div>
            </div>

            <!-- Левая ручка -->
            
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
</div>