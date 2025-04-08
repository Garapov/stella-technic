<div class="h-full grid grid-cols-8" x-data="construct">
    <div class="bg-white dark:bg-gray-700 p-12 col-span-3 flex flex-col items-center relative z-10" x-ref="projection">
        <div class="absolute right-0 top-[50%] p-4 bg-gray-200 dark:bg-gray-700 translate-y-[-50%] flex flex-col items-center gap-2 rounded-l-lg">
            <template x-for="(color, index) in colors" :key="index">
                <label
                    class="w-6 h-6 rounded-full cursor-pointer"
                    :class="{ 'opacity-25': color != selectedColor }"
                    :style="`background: ${color};`"
                ><input type="radio" name="colors" :value="color" x-model="selectedColor" class="hidden"></label>
            </template>
            <template x-for="(size, index) in sizes" :key="index">
                <label
                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300 cursor-pointer"
                    :class="{ 'opacity-25': size.value != selectedSize }"
                ><span x-text="size.name"></span><input type="radio" name="sizes" :value="size.value" x-model="selectedSize" class="hidden"></label>
            </template>

            <button type="button" class="px-3 py-2 text-xs font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @click="addRow">+</button>
        </div>
        <div class="relative h-full">
            <div class="absolute top-0 left-0 w-full h-full flex flex-col-reverse justify-start gap-1.5 p-4">
                <template x-for="row in addedRows" :key="index">
                    <div class="w-full">
                        <template x-if="row.size == 'small'">
                            <x-constructor-row />
                        </template>
                        <template x-if="row.size == 'medium'">
                            <x-constructor-row_medium />
                        </template>
                        <template x-if="row.size == 'large'">
                            <x-constructor-row_large />
                        </template>
                    </div>
                </template>
            </div>
            <img src="{{ asset('assets/stand.svg') }}" class="max-h-full" alt="">
        </div>
    </div>
    <div class="relative col-span-5 bg-gray-200 dark:bg-gray-700" x-ref="scene">
        <div class="absolute top-0 left-0 w-full h-full bg-gray-200 flex flex-col items-center justify-center gap-4 p-10" x-show="!isLoaded">
            <div class="text-lg text-gray-500">Загружаем 3D модели</div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full flex justify-center">
              <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="{width: progress + '%'}"> <span x-text="Math.floor(progress)"></span>%</div>
            </div>

        </div>
    </div>
</div>
