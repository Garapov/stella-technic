<div class="bg-white max-h-full overflow-auto p-4 col-span-4 absolute top-[calc(100%-40px)] lg:top-0 lg:relative flex flex-col justify-between gap-4 overflow-hidden" x-ref="settings">
    
    <div class="flex flex-col gap-4">
        <div class="text-xl italic font-semibold text-gray-900 dark:text-white">1. Выберите тип стойки</div>
        <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
            <template x-for="(deskType, index) in deskTypes" :key="index">
                <button type="button" class="grow px-5 py-2.5 text-sm font-medium rounded-lg text-center" x-text="deskType" @click="selectedDeskType = deskType" :class="{ 'text-slate-400 border border-slate-400 bg-slate-100': selectedDeskType != deskType, 'text-white border border-blue-700 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300': selectedDeskType == deskType}"></button>
            </template>
        </div>
        <div class="flex flex-col gap-1">
            <div class="italic font-semibold text-gray-900 dark:text-white">Размещение:</div>
            <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
                <template x-for="(position, index) in positions" :key="index">
                    <button type="button" class="grow px-3 py-1.5 text-sm font-medium rounded-lg text-center" x-text="position.name" @click="selectedPosition = position.value" :class="{ 'text-slate-400 border border-slate-400 bg-slate-100': position.value != selectedPosition, 'text-white border border-blue-700 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300':  position.value == selectedPosition}"></button>
                </template>
            </div>
        </div>

        <div class="flex items-start gap-4">
            <div class="grow flex flex-col gap-1">
                <div class="italic font-semibold text-gray-900 dark:text-white">Ширина:</div>
                <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
                    <template x-for="(wdth, index) in width" :key="index">
                        <button type="button" class="grow px-3 py-1.5 text-sm font-medium rounded-full text-center" x-text="wdth.name" @click="selectedWidth = wdth.value" :class="{ 'text-slate-400 border border-slate-400 bg-slate-100': wdth.value != selectedWidth, 'text-white border border-blue-700 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300':  wdth.value == selectedWidth, 'opacity-50 pointer-events-none': selectedHeight == 'high' && wdth.value == 'slim'}"></button>
                    </template>
                </div>
            </div>
            <div class="grow flex flex-col gap-1">
                <div class="italic font-semibold text-gray-900 dark:text-white">Высота:</div>
                <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
                    <template x-for="(hght, index) in height" :key="index">
                        <button type="button" class="grow px-3 py-1.5 text-sm font-medium rounded-full text-center" x-text="hght.name" @click="selectedHeight = hght.value" :class="{ 'text-slate-400 border border-slate-400 bg-slate-100': hght.value != selectedHeight, 'text-white border border-blue-700 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300': hght.value == selectedHeight, 'opacity-50 pointer-events-none': selectedWidth == 'slim' && hght.value == 'high'}"></button>
                    </template>
                </div>
            </div>
        </div>

        <div class="text-xl italic font-semibold text-gray-900 dark:text-white">2. Выберите тип и цвет ящиков в одном ряду</div>
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-300" role="alert">
            <div class="flex gap-4 items-center">
                <div class="w-36 flex flex-col items-end justify-between">
                    <div class="w-1/2 h-5 bg-slate-300"></div>
                    <div class="w-2/3 h-5 bg-slate-500"></div>
                    <div class="w-full h-5 bg-slate-700"></div>
                </div>
                <div>
                    <span class="font-medium">Обратите внимание!</span> Для устойчивости конструкции необходимо обязательно соблюдать порядок сборки! Допускается добавление ящика такого-же либо меньшего размера, относительно предыдущего ряда
                </div>
            </div>
        </div>
        <div class="flex item-center gap-2 rounded-md shadow-xs" role="group">
            <template x-for="(size, index) in sizes" :key="index">
                
                <div class="flex flex-col items-center gap-2 flex-1">
                    <div class="relative text-blue-500 cursor-pointer" x-data="{
                        popover: false,
                    }" @mouseover="popover = true"  @mouseover.away = "popover = false">
                        <x-css-info class="w-5 h-5" />
                        <div
                            x-show="popover"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute bottom-full z-10 mb-2 rounded-md bg-white px-3 py-2 text-sm font-medium text-slate-900 shadow-md"
                            :class="{
                                'left-0':  size.value == 'small',
                                'left-1/2 -translate-x-1/2 transform':  size.value == 'medium',
                                'right-0':  size.value == 'large'
                            }"
                        >
                            <div class="w-[350px] flex items-center gap-2">
                                <div class="w-[90px]">
                                    <img :src="`https://s3.stella-technic.ru/${boxes[`box_${size.value}_${selectedColor}`].gallery[0]}`" alt="">
                                </div>
                                <div class="flex-1">
                                    <div class="text-md font-bold" x-text="boxes[`box_${size.value}_${selectedColor}`].name"></div>
                                </div>
                            </div>
                            <div class="absolute -bottom-1  h-2 w-2 rotate-45 bg-white" :class="{
                                'left-0':  size.value == 'small',
                                'left-1/2 -translate-x-1/2 transform':  size.value == 'medium',
                                'right-0':  size.value == 'large'
                            }"></div>
                        </div>
                    </div>
                    <button type="button" class="grow px-5 py-2.5 text-sm font-medium rounded-lg text-center w-full" x-text="size.name" @click="selectedSize = size.value" :class="{ 'text-slate-400 border border-slate-400 bg-slate-100': size.value != selectedSize, 'text-white border border-blue-700 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300': size.value == selectedSize }"></button>
                </div>
            </template>
        </div>
        <div class="flex items-center justify-center gap-2 rounded-md shadow-xs" role="group">
            <template x-for="(color, index) in colors" :key="index">
                
                    <label
                        class="w-10 h-10 rounded-full cursor-pointer transition-all"
                        :class="{ 'border-8 border-white dark:border-gray-700': color != selectedColor }"
                        :style="`background: ${color};`"
                    >
                    <input type="radio" name="colors" :value="color" x-model="selectedColor" class="hidden"></label>
                
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
        {{-- <div class="text-xl text-bold" x-text="calculatedPrice"></div> --}}
        <template x-if="usedHeightPercent > 85">
            <button type="button" class="text-white bg-green-500 hover:bg-green-500/80 focus:ring-4 focus:outline-none focus:ring-green-500/50 font-medium rounded-lg text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:hover:bg-green-500/80 dark:focus:ring-green-500/40" @click="addToCart">
                <x-carbon-shopping-cart-plus class="w-6 h-6" />
            </button>
        </template>
    </div>
    <x-constructor.panel />
</div>
