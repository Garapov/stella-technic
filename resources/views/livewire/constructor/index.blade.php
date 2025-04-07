<div class="h-full grid grid-cols-8" x-data="construct">
    <div class="bg-white dark:bg-gray-700 p-4 overflow-auto col-span-3">
        <input type="color" x-model="color">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="addSmallBox">Добавить маленький ящик</button>
        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" @click="addMediumBox">Добавить средний ящик</button>
        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" @click="addLargeBox">Добавить большой ящик</button>
    </div>
    <div class="relative col-span-5 bg-blue-500" x-ref="scene">
        <div class="absolute top-0 left-0 w-full h-full bg-gray-200 flex flex-col items-center justify-center gap-4 p-10" x-show="!isLoaded">
            <div class="text-lg text-gray-500">Загружаем 3D модели</div>
            <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700 flex justify-center">
              <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="{width: progress + '%'}"> <span x-text="Math.floor(progress)"></span>%</div>
            </div>

        </div>
    </div>
</div>
