import * as THREE from "three";

// Импорт модулей
import {
    ROW_CONFIGS,
    MODELS,
    HELPER_BOX_SELECTOR,
    ADDED_ROWS,
} from "./constants";
import { createRowUI } from "./ui-manager";
import {
    setupThreeEnvironment,
    fitCameraToObjects,
    startRenderLoop,
    createFloor,
    updateProjectionCamera,
} from "./three-setup";

import {
    addDeskClone,
    changeDeskCloneVisibility,
    setPositionOnFloor,
    setPositionOnWall,
    changeDescHeight,
    changeDescWidth,
} from "./desk-manager";
import { loadModels, updateHeightCalculationBox } from "./model-loader";
import {
    animateBox,
    addBoxToScene,
    validateRowAddition,
    removeRowFromScene,
    unitsToMm,
} from "./row-manager";
import { updateDebugInfo, log } from "./debug-utils";
import { thickness } from "three/tsl";

export default ({
    selectedColor = "red",
    debugMode = false,
    desks = [],
    boxes = [],
    addedRows = [],
    embeded = false,
    selectedWidth = 'slim',
    selectedHeight = 'low',
    selectedDeskType = 'Односторонняя',
    selectedPosition = 'on_floor'
}) => {
    // Three.js контейнер
    const three = { scene: new THREE.Scene() };

    return {
        // Основное состояние
        isLoaded: false,
        progress: 0,
        error: null,
        selectedColor: selectedColor,
        embeded: embeded,
        addedRows: addedRows,
        colors: ["red", "green", "blue", "#ffeb00", "gray"],
        desks: desks,
        boxes: boxes,
        debugMode: debugMode,

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
        selectedDeskType: selectedDeskType,
        positions: [
            { name: "На полу", value: "on_floor" },
            { name: "На стене", value: "on_wall" },
            // { name: "На колесах", value: "on_wheels" },
        ],
        selectedPosition: selectedPosition,
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
        selectedWidth: selectedWidth,
        selectedWidthValue: 735,
        panelOpened: false,
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
        selectedHeight: selectedHeight,
        selectedHeightValue: 1515,
        // Информация о доступном пространстве
        usedHeightPercent: 0,
        remainingHeight: 1515,
        usedHeight: 0,

        // Свойства для шаблона
        maxSmallRowsToAdd: Math.floor(1515 / ROW_CONFIGS.small.height),
        maxMediumRowsToAdd: Math.floor(1515 / ROW_CONFIGS.medium.height),
        maxLargeRowsToAdd: Math.floor(1515 / ROW_CONFIGS.large.height),
        canAddSmallRow: true,
        canAddMediumRow: true,
        canAddLargeRow: true,

        // Инициализация компонента
        async init() {
            try {
                const container = this.$refs.scene;
                const projection = this.$refs.projection;
                this.adjustSceneHeight();

                // Настройка сцены и Three.js
                Object.assign(
                    three,
                    setupThreeEnvironment(container, projection),
                );

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
                startRenderLoop(this.debugMode); // Передаем флаг debugMode
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

                if (this.selectedWidth != 'slim') {
                    this.changeDescWidth(three, this.selectedWidth, '');
                }
                if (this.selectedHeight != 'low') {
                    this.changeDescHeight(three, this.selectedHeight, '');
                }
                if (this.selectedDeskType !== 'Односторонняя') {
                    this.changeDeskCloneVisibility(this.selectedDeskType, '');
                }
                if (this.selectedPosition != 'on_floor') {
                    this.changeDescPosition(three, this.selectedPosition, '');
                }

                if (this.selectedWidth == 'slim' && this.selectedHeight == 'low' && this.selectedDeskType == 'Односторонняя' && this.selectedPosition == 'on_floor') {
                    this.rebuildRows();
                }
                // if (!this.embeded) {
                //     this.rebuildRows();
                // }

                this.$watch("selectedDeskType", (newVal, oldVal) => {
                    this.changeDeskCloneVisibility(newVal, oldVal);
                });
                this.$watch("selectedPosition", (newVal, oldVal) => {
                    this.changeDescPosition(three, newVal, oldVal);
                });
                this.$watch("selectedHeight", (newVal, oldVal) => {
                    this.changeSelectedHeightValue(newVal);
                    this.changeDescHeight(three, newVal, oldVal);
                });
                this.$watch("selectedWidth", (newVal, oldVal) => {
                    this.changeSelectedWidthValue(newVal);
                    this.changeDescWidth(three, newVal, oldVal);
                });
            } catch (error) {
                this.error = error.message;
                console.error("Ошибка инициализации:", error);
            }
        },
        get selectedDesk() {
            let selector = this.selectedHeight + "_" + this.selectedWidth;
            return this.desks[selector];
        },
        get selectedBox() {
            let selector = "box_" + this.selectedSize;
            return this.boxes[selector];
        },
        get calculatedPrice() {
            let price = 0;
            price += this.selectedDesk.price;

            this.addedRows.forEach((box) => {
                price += this.boxes["box_" + box.size].price * ROW_CONFIGS[box.size][this.selectedWidth];
            });
            return new Intl.NumberFormat("ru-RU", {
                style: "currency",
                currency: "RUB",
            }).format(price);
        },
        openPanel() {
            this.panelOpened = true;
        },
        closePanel() {
            this.panelOpened = false;
        },
        addToCart() {

            let constructor_product = {
                id: this.selectedDesk.id,
                name: this.selectedDesk.name,
                boxes: {
                    small: {
                        id: this.boxes.box_small.id,
                        count: 0
                    },
                    medium:  {
                        id: this.boxes.box_medium.id,
                        count: 0
                    },
                    large:  {
                        id: this.boxes.box_large.id,
                        count: 0
                    },
                }
            }
            this.addedRows.forEach((row) => {
                switch (row.size) {
                    case 'small':
                        constructor_product.boxes.small.count += ROW_CONFIGS[row.size][this.selectedWidth];
                        break;
                    case 'medium':
                        constructor_product.boxes.medium.count += ROW_CONFIGS[row.size][this.selectedWidth];
                        break;
                    case 'large':
                        constructor_product.boxes.large.count += ROW_CONFIGS[row.size][this.selectedWidth];
                        break;
                
                    default:
                        break;
                }
                // Alpine.store("cart").addVariationToCart({
                //     count: ROW_CONFIGS[box.size][this.selectedWidth],
                //     variationId: this.boxes["box_" + box.size].id,
                //     name: `${this.boxes["box_" + box.size].name}`,
                // });
            });

            Alpine.store("cart").addConstructionToCart(constructor_product).then(res => {
                this.clearAll();
            });
        },
        clearAll() {
            const models = three.scene.getObjectByName("models");
            const clonedModels = three.scene.getObjectByName("clonedModels");

            for (let i = 0; i < this.addedRows.length + 1; i++) {
                const row = models.getObjectByName(`row_${i}`);
                const rowClone = clonedModels.getObjectByName(`row_${i}`);
                if (row && rowClone) {
                    models.remove(row);
                    clonedModels.remove(rowClone);
                }
            }
            this.addedRows = [];

            this.rebuildRows();
            this.updateHeightInfo();
            
        },
        updateHeightCalculationBox(three) {
            let models = three.scene.getObjectByName("models", true);
            if (!models) {
                console.error("Модели не найдены");
                return;
            }
            updateHeightCalculationBox(three, models).then(() => {
                this.updateHeightInfo();
                this.rebuildRows();
            });
        },
        changeDescHeight(three, newVal, oldVal) {
            changeDescHeight(three, newVal).then(() => {
                this.updateHeightCalculationBox(three);
            });
        },
        changeDescWidth(three, newVal, oldVal) {
            changeDescWidth(three, newVal).then(() => {
                this.updateHeightCalculationBox(three);
            });
        },
        changeSelectedHeightValue(newValue) {
            this.selectedHeightValue = this.height.filter(
                (height) => height.value === newValue,
            )[0].number;
            this.updateHeightInfo();
        },
        changeSelectedWidthValue(newValue) {
            this.selectedWidthValue = this.width.filter(
                (width) => width.value === newValue,
            )[0].number;
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
                    setPositionOnFloor(three).then(() => {
                        this.rebuildRows();
                    });
                    break;
                case "on_wall":
                    (this.selectedDeskType = this.deskTypes[0]),
                        setPositionOnWall(three).then(() => {
                            this.rebuildRows();
                        });
                    break;
                default:
                    setPositionOnFloor(three).then(() => {
                        this.rebuildRows();
                    });
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
            if (!container || !projection) return;

            const sceneHeight =
                window.innerHeight -
                (document.querySelector("header")?.offsetHeight || 0);

            container.style.height = `${sceneHeight}px`;
            projection.style.height = `${sceneHeight}px`;

            if (three.renderer && three.camera) {
                // Обновляем основной рендерер и камеру
                three.renderer.setSize(container.clientWidth, sceneHeight);
                three.camera.aspect = container.clientWidth / sceneHeight;
                three.camera.updateProjectionMatrix();
                three.renderer.shadowMap.enabled = true;
            }

            // Обновляем проекционный рендерер и камеру
            if (three.renderer_for_projection && three.cameraRTTProjection) {
                three.renderer_for_projection.setSize(
                    projection.clientWidth,
                    projection.clientHeight,
                );

                updateProjectionCamera(three);
            }
        },

        addFloorToScene(three) {
            return createFloor(three);
        },

        // Обновление информации о высоте
        updateHeightInfo() {
            // Находим HELPER_BOX_SELECTOR на сцене и получаем его высоту
            const helperBox = three.scene.getObjectByName(
                HELPER_BOX_SELECTOR,
                true,
            );
            let helperBoxHeight = this.selectedHeightValue; // Используем значение по умолчанию

            if (helperBox) {
                // Получаем размеры из бокса
                const box = new THREE.Box3().setFromObject(helperBox);
                const size = new THREE.Vector3();
                box.getSize(size);
                helperBoxHeight = size.y * 1000; // Конвертируем в мм
            } else {
                this.log(
                    "Предупреждение: HELPER_BOX_SELECTOR не найден на сцене, используется значение по умолчанию",
                    { selectedHeightValue: this.selectedHeightValue },
                );
            }

            const usedHeight = this.addedRows.reduce(
                (sum, row) => sum + ROW_CONFIGS[row.size].height,
                0,
            );

            this.usedHeight = usedHeight;
            this.remainingHeight = Math.round(helperBoxHeight - usedHeight);
            this.usedHeightPercent = Math.min(
                100,
                Math.round((usedHeight / helperBoxHeight) * 100),
            );

            // Обновляем флаги доступности размеров
            this.canAddSmallRow =
                this.remainingHeight >= ROW_CONFIGS.small.height;
            this.canAddMediumRow =
                this.remainingHeight >= ROW_CONFIGS.medium.height;
            this.canAddLargeRow =
                this.remainingHeight >= ROW_CONFIGS.large.height;

            // Обновляем максимальное количество рядов
            this.maxSmallRowsToAdd = Math.floor(
                this.remainingHeight / ROW_CONFIGS.small.height,
            );
            this.maxMediumRowsToAdd = Math.floor(
                this.remainingHeight / ROW_CONFIGS.medium.height,
            );
            this.maxLargeRowsToAdd = Math.floor(
                this.remainingHeight / ROW_CONFIGS.large.height,
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
            let row = addBoxToScene(
                three,
                this.selectedSize,
                this.selectedWidth,
                this.selectedHeight,
                this.selectedColor,
                this.addedRows,
                rowIndex,
                this.colors,
                (message, data) => this.log(message, data),
            );
            if (!this.embeded) {
                createRowUI(
                    three,
                    row,
                    this.colors,
                    () => {
                        this.removeRow(rowIndex);
                    },
                    (row, color) => {
                        this.changeRowColor(row, color);
                    },
                ).then((container) => {
                    

                    const rowBoundingBox = new THREE.Box3().setFromObject(row);
                    const containerBoundingBox = new THREE.Box3().setFromObject(
                        container,
                    );

                    // Устанавливаем позицию контейнера
                    container.position.set(
                        (rowBoundingBox.max.x - rowBoundingBox.min.x) / 2,
                        (rowBoundingBox.max.y - rowBoundingBox.min.y) / 2 + (containerBoundingBox.max.y - containerBoundingBox.min.y) / 4,
                        rowBoundingBox.max.z - rowBoundingBox.min.z,
                    );
                    row.add(container);
                });
            }
            return row;
        },

        changeRowColor(row, color) {

            let models = three.scene.getObjectByName('models');
            let clonedModels = three.scene.getObjectByName('clonedModels');

            if (!models || !clonedModels) return;

            let rowByName = models.getObjectByName(row.name);
            let rowByNameClone = clonedModels.getObjectByName(row.name);
            if (!rowByName || !rowByNameClone) return;
            rowByName.traverse(function (child) {
                if (child.name.includes("box")) {
                    child.material.color = new THREE.Color(color);
                }
            });
            rowByNameClone.traverse(function (child) {
                if (child.name.includes("box")) {
                    child.material.color = new THREE.Color(color);
                }
            });

            this.addedRows[rowByName.indexOnAddedRows].color = color;
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
            const row = this.addBox(this.addedRows.length);

            const boundingBox = new THREE.Box3().setFromObject(row);

            // Добавляем данные
            this.addedRows.push({
                size: this.selectedSize,
                color: this.selectedColor,
            });
            row.indexOnAddedRows = this.addedRows.length - 1;
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

            // three.objectsToTest = [];

            // Очищаем и восстанавливаем
            this.addedRows = [];

            // Добавляем ряды заново
            rowsData.forEach((data) => {
                this.selectedSize = data.size;
                this.selectedColor = data.color;
                this.addRow();
                // this.addedRows.push(data);
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
