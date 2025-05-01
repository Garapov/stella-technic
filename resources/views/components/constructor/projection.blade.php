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
            <p>Доступное пространство: <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full"><div class="text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="{width: usedHeightPercent + '%'}" x-bind:class="usedHeightPercent > 90 ? 'bg-red-500' : (usedHeightPercent > 70 ? ' цяbg-yellow-600' : 'bg-green-600')"> <span x-text="Math.floor(usedHeightPercent)"></span>%</div></div></p>
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
    <div class="relative h-full w-full" x-ref="projection"></div>
</div>
