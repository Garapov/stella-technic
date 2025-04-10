import * as THREE from "three";

// Импорт модулей
import { SHELF_HEIGHT, ROW_HEIGHTS, MODELS } from "./constants";
import {
    setupThreeEnvironment,
    fitCameraToObjects,
    startRenderLoop,
} from "./three-setup";
import { loadModels } from "./model-loader";
import {
    mmToUnits,
    canAddRow,
    addBoxToScene,
    validateRowAddition,
    removeRowFromScene,
} from "./row-manager";
import { updateDebugInfo, log } from "./debug-utils";

export default () => {
    // Three.js контейнер
    const three = { scene: new THREE.Scene() };

    return {
        // Основное состояние
        isLoaded: false,
        progress: 0,
        error: null,
        selectedColor: "red",
        selectedSize: "small",
        addedRows: [],
        colors: ["red", "green", "blue", "yellow", "gray"],
        debugMode: true,

        // Инициализация debugInfo
        debugInfo: {
            lastAction: "Инициализация",
            warnings: [],
            cameraPosition: { x: 0, y: 0, z: 0 },
            modelCount: 0,
            renderFrames: 0,
            memoryUsage: "N/A",
            fps: "N/A", // Добавляем это поле
            errorCount: 0,
            loadTime: 0,
            rowsPositions: [],
            allRowsOnScene: [],
        },

        // Размеры
        sizes: [
            { name: "V1", value: "small" },
            { name: "V2", value: "medium" },
            { name: "V3", value: "large" },
        ],

        // Информация о доступном пространстве
        usedHeightPercent: 0,
        remainingHeight: SHELF_HEIGHT,
        usedHeight: 0,

        // Свойства для шаблона
        maxSmallRowsToAdd: Math.floor(SHELF_HEIGHT / ROW_HEIGHTS.small),
        maxMediumRowsToAdd: Math.floor(SHELF_HEIGHT / ROW_HEIGHTS.medium),
        maxLargeRowsToAdd: Math.floor(SHELF_HEIGHT / ROW_HEIGHTS.large),
        canAddSmallRow: true,
        canAddMediumRow: true,
        canAddLargeRow: true,

        // Инициализация компонента
        async init() {
            try {
                const container = this.$refs.scene;
                this.adjustSceneHeight();

                // Настройка сцены и Three.js
                Object.assign(three, setupThreeEnvironment(container));

                // Загрузка моделей
                await loadModels(
                    three,
                    MODELS,
                    (message, data) => this.log(message, data),
                    (progress) => {
                        this.progress = Math.round(progress);
                    },
                );

                this.isLoaded = true;

                // Финальная настройка
                fitCameraToObjects(three);
                startRenderLoop(three, this.debugMode); // Передаем флаг debugMode
                this.updateHeightInfo();

                // Обработчики событий
                window.addEventListener("resize", () =>
                    this.adjustSceneHeight(),
                );

                // Периодическое обновление отладки
                if (this.debugMode) {
                    setInterval(() => this.updateDebugInfo(), 1000);
                }
            } catch (error) {
                this.error = error.message;
                console.error("Ошибка инициализации:", error);
            }
        },

        updateProgress(value) {
            this.progress = value;
        },

        // Настройка размеров сцены
        adjustSceneHeight() {
            const container = this.$refs.scene;
            const projection = this.$refs.projection;
            if (!container) return;

            const sceneHeight =
                window.innerHeight -
                (document.querySelector("header")?.offsetHeight || 0);

            container.style.height = `${sceneHeight}px`;
            projection.style.height = `${sceneHeight}px`;

            if (three.renderer && three.camera) {
                three.renderer.setSize(container.clientWidth, sceneHeight);
                three.camera.aspect = container.clientWidth / sceneHeight;
                three.camera.updateProjectionMatrix();
            }
        },

        // Обновление информации о высоте
        updateHeightInfo() {
            const usedHeight = this.addedRows.reduce(
                (sum, row) => sum + ROW_HEIGHTS[row.size],
                0,
            );

            this.usedHeight = usedHeight;
            this.remainingHeight = SHELF_HEIGHT - usedHeight;
            this.usedHeightPercent = Math.min(
                100,
                Math.round((usedHeight / SHELF_HEIGHT) * 100),
            );

            // Обновляем флаги доступности размеров
            this.canAddSmallRow = this.remainingHeight >= ROW_HEIGHTS.small;
            this.canAddMediumRow = this.remainingHeight >= ROW_HEIGHTS.medium;
            this.canAddLargeRow = this.remainingHeight >= ROW_HEIGHTS.large;

            // Обновляем максимальное количество рядов
            this.maxSmallRowsToAdd = Math.floor(
                this.remainingHeight / ROW_HEIGHTS.small,
            );
            this.maxMediumRowsToAdd = Math.floor(
                this.remainingHeight / ROW_HEIGHTS.medium,
            );
            this.maxLargeRowsToAdd = Math.floor(
                this.remainingHeight / ROW_HEIGHTS.large,
            );

            if (this.debugMode) this.updateDebugInfo();
        },

        // Обновление отладочной информации
        updateDebugInfo() {
            if (!this.debugMode) return;
            this.debugInfo = updateDebugInfo(
                three,
                this.debugInfo,
                this.debugMode,
                this.addedRows,
            );
        },

        // Логирование
        log(message, data) {
            this.debugInfo = log(message, data, this.debugMode, this.debugInfo);
        },

        // Добавление ящика
        addBox(rowIndex = null) {
            return addBoxToScene(
                three,
                this.selectedSize,
                this.selectedColor,
                this.addedRows,
                rowIndex,
                (message, data) => this.log(message, data),
            );
        },

        // Добавление нового ряда пользователем
        addRow() {
            this.updateHeightInfo();

            // Проверка на возможность добавления
            if (
                !validateRowAddition(
                    this.selectedSize,
                    this.addedRows,
                    this.remainingHeight,
                    (message, data) => this.log(message, data),
                )
            ) {
                return;
            }

            // Добавляем данные
            this.addedRows.push({
                size: this.selectedSize,
                color: this.selectedColor,
            });

            // Создаем ряд
            const row = this.addBox();
            this.updateHeightInfo();

            return row;
        },

        // Удаление ряда
        removeRow(index) {
            if (
                removeRowFromScene(
                    three,
                    index,
                    this.addedRows,
                    (message, data) => this.log(message, data),
                )
            ) {
                // Удаляем из данных и перестраиваем
                this.addedRows.splice(index, 1);
                this.updateHeightInfo();
                this.rebuildRows();
            }
        },

        // Перестройка рядов
        rebuildRows() {
            this.log(`Перестройка рядов (${this.addedRows.length})`);

            // Сохраняем данные
            const rowsData = [...this.addedRows];

            // Удаляем существующие ряды
            for (let i = 0; i < rowsData.length + 1; i++) {
                const row = three.scene.getObjectByName(`row_${i}`);
                if (row) three.scene.remove(row);
            }

            // Очищаем и восстанавливаем
            this.addedRows = [];

            // Добавляем ряды заново
            rowsData.forEach((data, idx) => {
                this.selectedSize = data.size;
                this.selectedColor = data.color;
                this.addedRows.push(data);
                this.addBox(idx);
            });

            this.log(`Перестройка завершена (${this.addedRows.length} рядов)`);
        },

        // Выбор цвета и размера
        selectColor(color) {
            this.selectedColor = color;
        },

        selectSize(size) {
            this.selectedSize = size;
        },

        toggleDebugMode() {
            this.debugMode = !this.debugMode;
            this.log(
                `Режим отладки ${this.debugMode ? "включен" : "выключен"}`,
            );

            // При включении отладки сразу запускаем обновление
            if (this.debugMode) {
                this.updateDebugInfo();
            }
        },

        // Очистка ресурсов
        cleanup() {
            window.removeEventListener("resize", this.adjustSceneHeight);

            if (three.renderer && this.$refs.scene) {
                this.$refs.scene.removeChild(three.renderer.domElement);
                three.renderer?.dispose();
                three.scene?.clear();
            }
        },
    };
};
