import * as THREE from "three";

// Импорт модулей
import { SHELF_HEIGHT, ROW_HEIGHTS, MODELS } from "./constants";
import {
    setupThreeEnvironment,
    fitCameraToObjects,
    startRenderLoop,
    createFloor,
} from "./three-setup";

import {
    addDeskClone,
    changeDeskCloneVisibility,
    setPositionOnFloor,
    setPositionOnWall,
    changeDescHeight,
    changeDescWidth,
} from "./desk-manager";
import { loadModels } from "./model-loader";
import {
    animateBox,
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

        addedRows: [
            // {
            //     size: "large",
            //     color: "red",
            // },
            // {
            //     size: "large",
            //     color: "green",
            // },
            // {
            //     size: "large",
            //     color: "blue",
            // },
            // {
            //     size: "medium",
            //     color: "#ffeb00",
            // },
            // {
            //     size: "medium",
            //     color: "gray",
            // },
            // {
            //     size: "medium",
            //     color: "red",
            // },
            // {
            //     size: "small",
            //     color: "green",
            // },
            // {
            //     size: "small",
            //     color: "blue",
            // },
            // {
            //     size: "small",
            //     color: "#ffeb00",
            // },
            // {
            //     size: "small",
            //     color: "gray",
            // },
        ],
        colors: ["red", "green", "blue", "#ffeb00", "gray"],
        debugMode: false,

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
            { name: "Маленький", value: "small" },
            { name: "Средний", value: "medium" },
            { name: "Большой", value: "large" },
        ],
        selectedSize: "small",
        deskTypes: ["Односторонняя", "Двусторонняя"],
        selectedDeskType: "Односторонняя",
        positions: [
            { name: "На полу", value: "on_floor" },
            { name: "На стене", value: "on_wall" },
            // { name: "На колесах", value: "on_wheels" },
        ],
        selectedPosition: "on_floor",
        width: [
            {
                name: "735 мм",
                value: "slim",
                number: 735,
            },
            {
                name: "1150 мм",
                value: "wide",
                number: 1150,
            },
        ],
        selectedWidth: "slim",
        height: [
            {
                name: "1515 мм",
                value: "low",
                number: 1515,
            },
            {
                name: "2020 мм",
                value: "high",
                number: 2020,
            },
        ],
        selectedHeight: "low",
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
                this.addFloorToScene(three);
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

                this.addDeskClone();

                this.rebuildRows();

                this.$watch("selectedDeskType", (newVal, oldVal) => {
                    this.changeDeskCloneVisibility(newVal, oldVal);
                });
                this.$watch("selectedPosition", (newVal, oldVal) => {
                    this.changeDescPosition(three, newVal, oldVal);
                });
                this.$watch("selectedHeight", (newVal, oldVal) => {
                    this.changeDescHeight(three, newVal, oldVal);
                    this.rebuildRows();
                });
                this.$watch("selectedWidth", (newVal, oldVal) => {
                    this.changeDescWidth(three, newVal, oldVal);
                    this.rebuildRows();
                });
            } catch (error) {
                this.error = error.message;
                console.error("Ошибка инициализации:", error);
            }
        },

        changeDescHeight(three, newVal, oldVal) {
            changeDescHeight(three, newVal);
        },
        changeDescWidth(three, newVal, oldVal) {
            changeDescWidth(three, newVal);
        },

        async changeDeskCloneVisibility(newVal, oldVal) {
            if (newVal === "Односторонняя")
                changeDeskCloneVisibility(three, false);
            if (newVal === "Двусторонняя") {
                if (this.selectedPosition == "on_wall") {
                    setPositionOnFloor(three).then((result) => {
                        this.selectedPosition =
                            this.positions[0].value ?? "on_floor";
                        changeDeskCloneVisibility(three, true);
                    });
                } else {
                    changeDeskCloneVisibility(three, true);
                }
            }
        },
        changeDescPosition(three, newVal, oldVal) {
            switch (newVal) {
                case "on_floor":
                    setPositionOnFloor(three);
                    break;
                case "on_wall":
                    (this.selectedDeskType = this.deskTypes[0]),
                        setPositionOnWall(three);
                    break;
                default:
                    setPositionOnFloor(three);
            }
        },
        addDeskClone() {
            addDeskClone(three);
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
                three.renderer.shadowMap.enabled = true;
            }
        },

        addFloorToScene(three) {
            return createFloor(three);
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

        tookOutBox({ rowIndex, boxIndex }) {
            return animateBox({
                three,
                rowIndex,
                boxIndex,
            });
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
                this.selectedWidth,
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

            // Создаем ряд
            const row = this.addBox();
            // Добавляем данные
            this.addedRows.push({
                size: this.selectedSize,
                color: this.selectedColor,
            });
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
                const models = three.scene.getObjectByName("models");
                const clonedModels =
                    three.scene.getObjectByName("clonedModels");
                const row = models.getObjectByName(`row_${i}`);
                const rowClone = clonedModels.getObjectByName(`row_${i}`);
                if (row && rowClone) {
                    models.remove(row);
                    clonedModels.remove(rowClone);
                }
            }

            // Очищаем и восстанавливаем
            this.addedRows = [];

            // Добавляем ряды заново
            rowsData.forEach((data) => {
                this.selectedSize = data.size;
                this.selectedColor = data.color;
                this.addBox();
                this.addedRows.push(data);
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
