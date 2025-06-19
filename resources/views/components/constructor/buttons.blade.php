<div class="bg-white dark:bg-gray-700 p-4 col-span-2 relative flex flex-col justify-between gap-4 relative overflow-hidden" x-ref="settings">
    
    <div class="flex flex-col gap-4">
        <div class="text-xl italic font-semibold text-gray-900 dark:text-white">1. Выберите тип стойки</div>
        <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
            <template x-for="(deskType, index) in deskTypes" :key="index">
                <button type="button" class="grow px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" x-text="deskType" @click="selectedDeskType = deskType" :class="{ 'opacity-50': selectedDeskType != deskType }"></button>
            </template>
        </div>
        <div class="flex flex-col gap-1">
            <div class="italic font-semibold text-gray-900 dark:text-white">Размещение:</div>
            <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
                <template x-for="(position, index) in positions" :key="index">
                    <button type="button" class="grow px-3 py-1.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" x-text="position.name" @click="selectedPosition = position.value" :class="{ 'opacity-50': position.value != selectedPosition }"></button>
                </template>
            </div>
        </div>

        <div class="flex items-start gap-4">
            <div class="grow flex flex-col gap-1">
                <div class="italic font-semibold text-gray-900 dark:text-white">Ширина:</div>
                <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
                    <template x-for="(wdth, index) in width" :key="index">
                        <button type="button" class="grow px-3 py-1.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" x-text="wdth.name" @click="selectedWidth = wdth.value" :class="{ 'opacity-50': wdth.value != selectedWidth }"></button>
                    </template>
                </div>
            </div>
            <div class="grow flex flex-col gap-1">
                <div class="italic font-semibold text-gray-900 dark:text-white">Высота:</div>
                <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
                    <template x-for="(hght, index) in height" :key="index">
                        <button type="button" class="grow px-3 py-1.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" x-text="hght.name" @click="selectedHeight = hght.value" :class="{ 'opacity-50': hght.value != selectedHeight }"></button>
                    </template>
                </div>
            </div>
        </div>

        <div class="text-xl italic font-semibold text-gray-900 dark:text-white">2. Выберите тип и цвет ящиков в одном ряду</div>
        <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
            <template x-for="(size, index) in sizes" :key="index">
                <button type="button" class="grow px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" x-text="size.name" @click="selectedSize = size.value" :class="{ 'opacity-50': size.value != selectedSize }"></button>
            </template>
        </div>
        <div class="flex items-center justify-center gap-2 rounded-md shadow-xs" role="group">
            <template x-for="(color, index) in colors" :key="index">
                <label
                    class="w-10 h-10 rounded-full cursor-pointer transition-all"
                    :class="{ 'border-8 border-white dark:border-gray-700': color != selectedColor }"
                    :style="`background: ${color};`"
                ><input type="radio" name="colors" :value="color" x-model="selectedColor" class="hidden"></label>
            </template>
        </div>
        <div class="flex justify-center gap-4">
            <button type="button" class="text-white bg-green-500 hover:bg-green-500/80 focus:ring-4 focus:outline-none focus:ring-green-500/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:hover:bg-green-500/80 dark:focus:ring-green-500/40"  @click="addRow">
                <x-carbon-add-alt class="w-4 h-4 me-2 -ms-1" />
                Добавить
            </button>
            <button type="button" class="text-white bg-red-500 hover:bg-red-500/80 focus:ring-4 focus:outline-none focus:ring-red-500/50 font-medium rounded-lg text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:hover:bg-red-500/80 dark:focus:ring-red-500/40" @click="clearAll">
                <x-carbon-clean class="w-6 h-6" />
            </button>
            @if (auth()->user() && auth()->user()->hasRole('super_admin'))
                <button type="button" class="text-white bg-green-500 hover:bg-green-500/80 focus:ring-4 focus:outline-none focus:ring-green-500/50 font-medium rounded-lg text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:hover:bg-green-500/80 dark:focus:ring-green-500/40" @click="openPanel">
                    <x-carbon-save class="w-6 h-6" />
                </button>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div class="text-xl text-bold" x-text="calculatedPrice"></div>
        <template x-if="100 - usedHeightPercent < 10">
            <button type="button" class="text-white bg-green-500 hover:bg-green-500/80 focus:ring-4 focus:outline-none focus:ring-green-500/50 font-medium rounded-lg text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:hover:bg-green-500/80 dark:focus:ring-green-500/40" @click="addToCart">
                <x-carbon-shopping-cart-plus class="w-6 h-6" />
            </button>
        </template>
    </div>
    <x-constructor.panel />
</div>
