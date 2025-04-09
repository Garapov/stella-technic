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
        <div class="relative h-full">
            <div class="absolute top-0 left-0 w-full h-full flex flex-col-reverse justify-start gap-1.5 p-4">
                <template x-for="(row, index) in addedRows" :key="index">
                    <div class="w-full relative">
                        <template x-if="row.size == 'small'">
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
