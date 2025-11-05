{{-- {{ dd($values) }} --}}
<div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 flex flex-col gap-3" wire:loading.class="opacity-50 pointer-events-none" x-data="{
    isOpened: true,
    minPrice: {{ $variations->min('price') }},
    maxPrice: {{ $variations->max('price') }},
    value: @js(isset($this->filterParams["price"]['$between']) ? $this->filterParams["price"]['$between'] : [$variations->min('price'), $variations->max('price')]),
    dragThumb: null,
    trackRect: null,
    get minThumbStyle() {
        return {
            left: `${this.value[0] * 100 / this.maxPrice}%`,
            transform: `translate(-${this.value[0] * 100 / this.maxPrice}%, 0)`
        };
    },

    get maxThumbStyle() {
        return {
            left: `${this.value[1] * 100 / this.maxPrice}%`,
            transform: `translate(-${this.value[1] * 100 / this.maxPrice}%, 0)`
        };
    },

    get activeRangeStyle() {
        const min_left = this.value[0] * 100 / this.maxPrice;
        const max_left = this.value[1] * 100 / this.maxPrice;
        const width = 100 - min_left - (100 - max_left);
        return {
            left: `${min_left}%`,
            width: `${width}%`
        };
    },

    init() {
        console.log(this.minPrice, this.maxPrice, this.value, this.minThumbStyle, this.maxThumbStyle);
    },
    setFilter() {
        {{-- this.onChange(); --}}
        {{-- $wire.set('filters["price"]["$between"]', this.value); --}}

        if (this.value[0] < this.minPrice) {
            this.value[0] = this.minPrice;
        } else if (this.value[0] > this.value[1]) {
            this.value[0] = this.value[1];
        };
        if (this.value[1] > this.maxPrice) {
            this.value[1] = this.maxPrice;
        } else if (this.value[1] < this.value[0]) {
            this.value[1] = this.value[0];
        }
        $wire.setPrice(this.value);
    },

    startDrag(e) {
        e.preventDefault();
        this.trackRect = this.$refs.track.getBoundingClientRect();



        const moveHandler = (event) => {
            let clientX = event.type.includes('touch') ? event.touches[0].clientX : event.clientX;
            let percent = (clientX - this.trackRect.left) / this.trackRect.width;
            
            let result = Math.round(this.maxPrice * percent);
            console.log(clientX, percent, this.maxPrice * percent);
            if (this.dragThumb === 'min') {
                if (result < this.minPrice) {
                    this.value[0] = this.minPrice;
                } else if (result > this.value[1]) {
                    this.value[0] = this.value[1];
                } else {
                    this.value[0] = result;
                }
            }
            if (this.dragThumb === 'max') {
                if (result > this.maxPrice) {
                    this.value[1] = this.maxPrice;
                } else if (result < this.value[0]) {
                    this.value[1] = this.value[0];
                } else {
                    this.value[1] = result;
                }
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
}">

    <div class="flex items-center justify-between cursor-pointer" @click="isOpened = !isOpened">
        <span class="text-[0.9rem] font-semibold dark:text-white">Цена</span>
        <span class="block min-w-6 max-w-6 min-h-6 max-h-6" :class="{'rotate-180': isOpened}">
            <x-eva-arrow-ios-downward-outline class="w-full h-full"  />
        </span>
    </div>
    <div wire:ignore x-data="{}" class="relative w-full max-w-xl mx-auto" x-show="isOpened">

        <div class="flex items-center gap-4 mb-3">
            <input type="number" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="От" x-model="value[0]" @blur="setFilter">
            <input type="number" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="До" x-model="value[1]" @blur="setFilter">
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
    </div>
</div>