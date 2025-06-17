<div class="relative col-span-4 bg-gray-700 dark:bg-gray-700" x-ref="scene">
    <div class="absolute top-0 left-0 w-full h-full bg-gray-700 dark:bg-gray-700 flex flex-col items-center justify-center gap-4 p-10" x-show="!isLoaded" wire:ignore>
        <div class="text-lg text-gray-500">Загружаем 3D модели</div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full flex justify-center">
          <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="{width: progress + '%'}"> <span x-text="Math.floor(progress)"></span>%</div>
        </div>

    </div>
</div>
