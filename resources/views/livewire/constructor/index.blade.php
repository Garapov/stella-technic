<div class="h-full grid grid-cols-9" x-data="construct">
    <div class="bg-white dark:bg-gray-700 col-span-3 flex flex-col items-center relative" x-ref="projection">

        <!-- Кнопка для включения режима отладки, когда режим выключен -->
        <button @click="toggleDebugMode" x-show="!debugMode" class="debug-toggle">
            Режим отладки
        </button>

        <!-- Панель отладочной информации, отображается только когда режим включен -->
        <div x-show="debugMode" class="debug-panel">
            <h3>Отладочная информация</h3>
            <button @click="toggleDebugMode">Скрыть</button>

            <div class="debug-section">
                <h4>Общая информация</h4>
                <p>Последнее действие: <span x-text="debugInfo.lastAction"></span></p>
                <p>Время загрузки: <span x-text="debugInfo.loadTime"></span> сек</p>
                <p>Кадров: <span x-text="debugInfo.renderFrames"></span></p>
                <p>FPS: <span x-text="debugInfo.fps || 'N/A'"></span></p>
                <p>Использование памяти: <span x-text="debugInfo.memoryUsage"></span></p>
            </div>

            <div class="debug-section">
                <h4>Камера</h4>
                <p>X: <span x-text="debugInfo.cameraPosition.x"></span></p>
                <p>Y: <span x-text="debugInfo.cameraPosition.y"></span></p>
                <p>Z: <span x-text="debugInfo.cameraPosition.z"></span></p>
            </div>

            <div class="debug-section">
                <h4>Сцена</h4>
                <p>Всего объектов: <span x-text="debugInfo.modelCount"></span></p>
                <p>Доступное пространство: <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full"><div class="text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="{width: usedHeightPercent + '%'}" x-bind:class="usedHeightPercent > 90 ? 'bg-red-500' : (usedHeightPercent > 70 ? 'bg-yellow-600' : 'bg-green-600')"> <span x-text="Math.floor(usedHeightPercent)"></span>%</div></div></p>
                <p>Занято: <span x-text="`${usedHeight} мм`"></span></p>
                <p>Осталось: <span x-text="`${remainingHeight} мм`"></span></p>
                <p>Можно добавить еще: <span x-text="`V1: ${maxSmallRowsToAdd} шт. V2: ${maxMediumRowsToAdd} шт. V3: ${maxLargeRowsToAdd} шт.`"></span></p>
            </div>

            <div class="debug-section">
                <h4>Объекты рядов</h4>
                <div x-show="debugInfo.rowsPositions.length > 0">
                    <template x-for="row in debugInfo.rowsPositions" :key="row.index">
                        <div class="debug-row">
                            <p>Ряд #<span x-text="row.index"></span>:
                               <span x-text="row.size"></span> |
                               <span x-text="row.color"></span> |
                               Позиция: <span x-text="JSON.stringify(row.position)"></span>
                            </p>
                        </div>
                    </template>
                </div>
                <div x-show="debugInfo.rowsPositions.length === 0">
                    <p>Нет рядов</p>
                </div>
            </div>

            <div class="debug-section">
                <h4>Все ряды на сцене</h4>
                <div x-show="debugInfo.allRowsOnScene && debugInfo.allRowsOnScene.length > 0">
                    <template x-for="(row, index) in debugInfo.allRowsOnScene" :key="index">
                        <div class="debug-row">
                            <p>
                               <span x-text="row.name"></span> |
                               Позиция: <span x-text="JSON.stringify(row.position)"></span>
                            </p>
                        </div>
                    </template>
                </div>
                <div x-show="!debugInfo.allRowsOnScene || debugInfo.allRowsOnScene.length === 0">
                    <p>Нет рядов на сцене</p>
                </div>
            </div>

            <div class="debug-section">
                <h4>Предупреждения</h4>
                <div x-show="debugInfo.warnings.length > 0">
                    <template x-for="(warning, index) in debugInfo.warnings" :key="index">
                        <div class="debug-warning">
                            <p><span x-text="warning.time"></span>: <span x-text="warning.message"></span></p>
                        </div>
                    </template>
                </div>
                <div x-show="debugInfo.warnings.length === 0">
                    <p>Нет предупреждений</p>
                </div>
            </div>


        </div>
        <div class="relative h-full w-full" x-ref="projection">
            <!-- <div class="absolute top-0 left-0 w-full h-full flex flex-col-reverse justify-start gap-1.5 p-4">
                <template x-for="(row, index) in addedRows" :key="index">
                    <div class="w-full relative">
                        <template x-if="row.size == 'small'" >
                            <x-constructor-row />
                        </template>
                        <template x-if="row.size == 'medium'">
                            <x-constructor-row_medium />
                        </template>
                        <template x-if="row.size == 'large'">
                            <x-constructor-row_large />
                        </template>
                        <button type="button" class="absolute left-full top-[50%] px-3 py-2 text-xs font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @click="removeRow(index)">x</button>
                    </div>
                </template>
            </div>
            <img src="{{ asset('assets/stand.svg') }}" class="max-h-full" alt=""> -->
        </div>
    </div>
    <div class="bg-white dark:bg-gray-700 py-12 px-4 col-span-2 flex flex-col gap-4 relative" x-ref="settings">
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
        <div class="flex item-center justify-center gap-2 rounded-md shadow-xs" role="group">
            <template x-for="(color, index) in colors" :key="index">
                <label
                    class="w-10 h-10 rounded-full cursor-pointer transition-all"
                    :class="{ 'border-8 border-white dark:border-gray-700': color != selectedColor }"
                    :style="`background: ${color};`"
                ><input type="radio" name="colors" :value="color" x-model="selectedColor" class="hidden"></label>
            </template>
        </div>
        <div class="flex items-center justify-center">
            <button type="button" class="text-white bg-green-500 hover:bg-green-500/80 focus:ring-4 focus:outline-none focus:ring-green-500/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:hover:bg-green-500/80 dark:focus:ring-green-500/40 me-2 mb-2"  @click="addRow">
                <x-carbon-add-alt class="w-4 h-4 me-2 -ms-1" />
                Добавить
            </button>
        </div>


    </div>
    <div class="relative col-span-4 bg-gray-700 dark:bg-gray-700" x-ref="scene">
        <div class="absolute top-0 left-0 w-full h-full bg-gray-700 dark:bg-gray-700 flex flex-col items-center justify-center gap-4 p-10" x-show="!isLoaded">
            <div class="text-lg text-gray-500">Загружаем 3D модели</div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full flex justify-center">
              <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="{width: progress + '%'}"> <span x-text="Math.floor(progress)"></span>%</div>
            </div>

        </div>
    </div>
</div>
