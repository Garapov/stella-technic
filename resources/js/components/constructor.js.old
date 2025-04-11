import * as THREE from "three";
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { OBJLoader } from "three/addons/loaders/OBJLoader.js";
import { MTLLoader } from "three/addons/loaders/MTLLoader.js";
import Toastify from "toastify-js";
import { gsap } from "gsap";

// Константы
const SHELF_HEIGHT = 1515; // мм
const ROW_HEIGHTS = { small: 110, medium: 156, large: 178 };
const SCALE_FACTOR = 0.00095;
const BASE_POSITIONS = { small: 0.176, medium: 0.213, large: 0.23 };
const ROW_CONFIGS = {
    small: { selector: "box", count: 6, offset: -0.106 },
    medium: { selector: "box_medium", count: 4, offset: -0.16 },
    large: { selector: "box_large", count: 3, offset: -0.215 },
};

// Конфигурация моделей
const MODELS = [
    {
        name: "shelf",
        obj: "/assets/models/shelf.obj",
        mtl: "/assets/models/shelf.mtl",
    },
    {
        name: "row",
        obj: "/assets/models/row.obj",
        mtl: "/assets/models/row.mtl",
        position: { x: 0, y: 0.05, z: -0.25 },
    },
];

export default () => {
    // Three.js контейнер
    const three = {
        scene: new THREE.Scene(),
        camera: null,
        renderer: null,
        controls: null,
        originalRow: null,
        lastRowPosition: new THREE.Vector3(0, 0, 0),
    };

    return {
        // Основное состояние
        isLoaded: false,
        progress: 0,
        error: null,
        selectedColor: "red",
        selectedSize: "small",
        addedRows: [],
        colors: ["red", "green", "blue", "yellow", "gray"],
        debugMode: false,

        // Инициализация debugInfo со всеми необходимыми свойствами
        debugInfo: {
            lastAction: "Инициализация",
            warnings: [],
            cameraPosition: { x: 0, y: 0, z: 0 },
            modelCount: 0,
            renderFrames: 0,
            memoryUsage: "N/A",
            errorCount: 0,
            loadTime: 0,
            rowsPositions: [], // Инициализируем пустым массивом
            allRowsOnScene: [],
        },

        sizes: [
            { name: "V1", value: "small" },
            { name: "V2", value: "medium" },
            { name: "V3", value: "large" },
        ],

        // Информация о доступном пространстве
        usedHeightPercent: 0,
        remainingHeight: SHELF_HEIGHT,
        usedHeight: 0,

        // Добавляем эти свойства для шаблона
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

                // Настройка сцены
                three.camera = new THREE.PerspectiveCamera(
                    45,
                    container.clientWidth / container.clientHeight,
                    0.1,
                    2000,
                );
                three.camera.position.z = 1;

                // Рендерер
                three.renderer = new THREE.WebGLRenderer({
                    antialias: true,
                    alpha: true,
                });
                three.renderer.setSize(
                    container.clientWidth,
                    container.clientHeight,
                );
                three.renderer.setPixelRatio(window.devicePixelRatio);
                container.appendChild(three.renderer.domElement);

                // Освещение
                this.setupLights();

                // Управление камерой
                three.controls = new OrbitControls(
                    three.camera,
                    three.renderer.domElement,
                );
                three.controls.enableDamping = true;
                three.controls.dampingFactor = 0.05;

                // Загрузка моделей
                await this.loadModels();

                // Запуск рендеринга и обработчики
                this.startRenderLoop();
                this.updateHeightInfo();
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

        // Настройка освещения
        setupLights() {
            // Базовый свет
            three.scene.add(new THREE.AmbientLight(0xffffff, 0.3));

            // Направленный свет
            const mainLight = new THREE.DirectionalLight(0xffffff, 1.0);
            mainLight.position.set(1, 4, 2);
            three.scene.add(mainLight);

            // Дополнительное освещение для металлического эффекта
            [
                {
                    type: "spot",
                    color: 0xffffff,
                    intensity: 0.8,
                    position: [-2, 5, 4],
                },
                {
                    type: "directional",
                    color: 0xadd8e6,
                    intensity: 0.6,
                    position: [-3, 1, -5],
                },
                {
                    type: "directional",
                    color: 0xffffee,
                    intensity: 0.4,
                    position: [3, 0, 3],
                },
            ].forEach((light) => {
                const newLight =
                    light.type === "spot"
                        ? new THREE.SpotLight(
                              light.color,
                              light.intensity,
                              100,
                              Math.PI / 4,
                              0.5,
                          )
                        : new THREE.DirectionalLight(
                              light.color,
                              light.intensity,
                          );

                newLight.position.set(...light.position);
                three.scene.add(newLight);
            });

            // Полусферическое освещение
            three.scene.add(new THREE.HemisphereLight(0xffffff, 0xcccccc, 0.3));
        },

        // Загрузка всех моделей
        async loadModels() {
            // Загрузка всех моделей параллельно
            await Promise.all(MODELS.map((model) => this.loadModel(model)));

            this.isLoaded = true;
            this.fitCameraToObjects();
            this.log("Все модели загружены");
        },

        // Загрузка одной модели
        async loadModel(model) {
            this.log(`Загрузка модели ${model.name}`);

            // Загрузка материалов
            const materials = await new Promise((resolve, reject) => {
                new MTLLoader().load(
                    model.mtl,
                    (materials) => resolve(materials),
                    undefined,
                    (error) =>
                        reject(
                            new Error(
                                `Ошибка загрузки материалов для ${model.name}: ${error.message}`,
                            ),
                        ),
                );
            });

            materials.preload();

            // Загрузка объекта
            const object = await new Promise((resolve, reject) => {
                const objLoader = new OBJLoader();
                objLoader.setMaterials(materials);
                objLoader.load(
                    model.obj,
                    (object) => resolve(object),
                    (xhr) => {
                        this.progress = (xhr.loaded / xhr.total) * 100;
                    },
                    (error) =>
                        reject(
                            new Error(
                                `Ошибка загрузки модели ${model.name}: ${error.message}`,
                            ),
                        ),
                );
            });

            // Настройка материалов
            object.traverse((child) => {
                if (child.isMesh) {
                    child.castShadow = false;
                    child.receiveShadow = true;
                    child.material.color.set("#ffffff");
                }
            });

            // Применение позиции
            if (model.position) {
                const { x = 0, y = 0, z = 0 } = model.position;
                object.position.set(x, y, z);
            }

            // Специальная обработка для row модели
            if (model.name === "row") {
                this.setupRowModel(object);
            }

            // Добавление на сцену
            three.scene.add(object);
            model.object = object;

            return object;
        },

        // Настройка модели ряда
        setupRowModel(object) {
            // Настройка видимости и позиции компонентов
            ["box", "box_medium", "box_large"].forEach((name) => {
                const box = object.getObjectByName(name, true);
                if (box) {
                    box.position.y = 0.3;
                    box.visible = false;
                }
            });

            object.visible = false;
            three.originalRow = object.clone();
        },

        // Подгонка камеры к объектам
        fitCameraToObjects() {
            const box = new THREE.Box3();

            // Расширяем бокс всеми объектами сцены
            three.scene.traverse((obj) => {
                if (obj.isMesh || obj.isGroup) box.expandByObject(obj);
            });

            if (box.isEmpty()) return;

            const size = box.getSize(new THREE.Vector3());
            const center = box.getCenter(new THREE.Vector3());

            // Установка целевой точки и позиции камеры
            three.controls.target.copy(center);

            const maxDim = Math.max(size.x, size.y, size.z);
            const fov = three.camera.fov * (Math.PI / 180);
            const distance = Math.abs(maxDim / (2 * Math.tan(fov / 2))) * 1.5;

            const direction = new THREE.Vector3(0, 0, 1).applyQuaternion(
                three.camera.quaternion,
            );
            three.camera.position
                .copy(center)
                .add(direction.multiplyScalar(distance));
            three.camera.updateProjectionMatrix();
            three.controls.update();
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

        // Запуск цикла рендеринга
        startRenderLoop() {
            const animate = () => {
                requestAnimationFrame(animate);

                if (three.controls) three.controls.update();
                if (three.renderer && three.scene && three.camera) {
                    three.renderer.render(three.scene, three.camera);
                }
            };

            animate();
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

            // Информация о камере (проверяем наличие камеры)
            if (three.camera) {
                this.debugInfo.cameraPosition = {
                    x: three.camera.position.x.toFixed(3),
                    y: three.camera.position.y.toFixed(3),
                    z: three.camera.position.z.toFixed(3),
                };
            }

            // Информация о сцене
            if (three.scene) {
                let objectCount = 0;
                three.scene.traverse(() => objectCount++);
                this.debugInfo.modelCount = objectCount;

                // Информация об объектах
                this.debugInfo.sceneObjects = [];
                this.debugInfo.allRowsOnScene = [];

                three.scene.children.forEach((child) => {
                    if (child.name?.startsWith("row_")) {
                        this.debugInfo.sceneObjects.push({
                            name: child.name,
                            type: child.type,
                            visible: child.visible,
                            position: this.formatPosition(child.position),
                            isRow: true,
                        });

                        this.debugInfo.allRowsOnScene.push({
                            name: child.name,
                            position: this.formatPosition(child.position),
                        });
                    } else {
                        this.debugInfo.sceneObjects.push({
                            name: child.name || "unnamed",
                            type: child.type,
                            visible: child.visible,
                            position: this.formatPosition(child.position),
                        });
                    }
                });
            }

            // Информация о рядах
            this.debugInfo.rowsPositions = this.addedRows.map((row, index) => {
                const rowName = `row_${index}`;
                const rowObj = three.scene.getObjectByName(rowName);

                return {
                    index,
                    size: row.size,
                    color: row.color,
                    name: rowName,
                    found: Boolean(rowObj),
                    position: rowObj
                        ? this.formatPosition(rowObj.position)
                        : "not found",
                };
            });

            // Информация о памяти
            if (window.performance?.memory) {
                const memory = window.performance.memory;
                this.debugInfo.memoryUsage = `${Math.round(memory.usedJSHeapSize / 1048576)} MB / ${Math.round(memory.jsHeapSizeLimit / 1048576)} MB`;
            }

            this.debugInfo.renderFrames++;
        },

        // Форматирование позиции для вывода
        formatPosition(position) {
            return Object.fromEntries(
                ["x", "y", "z"].map((axis) => [
                    axis,
                    position[axis].toFixed(3),
                ]),
            );
        },

        // Логирование с учетом режима отладки
        log(message, data) {
            if (!this.debugMode) return;

            const timestamp = new Date().toLocaleTimeString();
            this.debugInfo.lastAction = `${timestamp}: ${message}`;

            if (data?.warning) {
                this.debugInfo.warnings.push({
                    time: timestamp,
                    message: data.warning,
                });
                if (this.debugInfo.warnings.length > 10)
                    this.debugInfo.warnings.shift();
            }

            console.log(`[DEBUG] ${message}`, data);
        },

        // Конвертация размеров
        mmToUnits(mm) {
            return mm * SCALE_FACTOR;
        },
        unitsToMm(units) {
            return units / SCALE_FACTOR;
        },

        // Проверка возможности добавления ряда
        canAddRow(size) {
            return this.remainingHeight >= ROW_HEIGHTS[size];
        },

        // Расчет позиции для ряда
        calculateRowPosition(rowIndex) {
            const rows = this.addedRows;
            const basePosition = BASE_POSITIONS[rows[0]?.size || "small"];

            // Для первого ряда используем базовую позицию
            if (rowIndex === 0) return basePosition;

            // Для последующих добавляем высоту предыдущих
            return rows
                .slice(0, rowIndex)
                .reduce(
                    (pos, row) => pos + this.mmToUnits(ROW_HEIGHTS[row.size]),
                    basePosition,
                );
        },

        // Создание ящиков для ряда
        createBoxesForRow(rowClone, originalBox, config) {
            const { count, offset } = config;

            Array.from({ length: count }).forEach((_, i) => {
                // Клонирование бокса
                const boxClone = originalBox.clone();
                boxClone.visible = true;

                // Настройка материала
                if (boxClone.material) {
                    boxClone.material = Array.isArray(boxClone.material)
                        ? boxClone.material.map((m) => {
                              const clone = m.clone();
                              clone.color.set(this.selectedColor);
                              return clone;
                          })
                        : (() => {
                              const clone = boxClone.material.clone();
                              clone.color.set(this.selectedColor);
                              return clone;
                          })();
                }

                // Позиция и имя
                boxClone.position.set(
                    originalBox.position.x + i * offset,
                    originalBox.position.y,
                    originalBox.position.z,
                );

                const boxName = `box_${i}_${Math.random().toString(36).slice(2, 9)}`;
                boxClone.name = boxName;

                // Добавление и анимация
                rowClone.add(boxClone);
                gsap.to(rowClone.getObjectByName(boxName).position, {
                    y: 0,
                    duration: 0.15,
                    delay: i * 0.05,
                    ease: "power3.inOut",
                });
            });
        },

        // Добавление ящика
        addBox(rowIndex = null) {
            if (!three.originalRow) {
                console.error("Оригинальная модель ряда не найдена");
                return;
            }

            // Получаем параметры для текущего размера
            const config = ROW_CONFIGS[this.selectedSize];

            // Клонирование ряда
            const rowClone = three.originalRow.clone();
            rowClone.visible = true;

            // Получаем нужный тип бокса
            const originalBox = rowClone.getObjectByName(config.selector, true);
            if (!originalBox) {
                console.error(
                    `Бокс ${config.selector} не найден в модели ряда`,
                );
                return;
            }

            // Создаем боксы для ряда
            this.createBoxesForRow(rowClone, originalBox, config);

            // Определяем индекс и рассчитываем позицию
            const index = rowIndex !== null ? rowIndex : this.addedRows.length;
            const yPosition = this.calculateRowPosition(index);

            // Устанавливаем позицию и имя
            rowClone.position.set(
                three.originalRow.position.x,
                yPosition,
                three.originalRow.position.z,
            );

            rowClone.name = `row_${index}`;

            // Добавляем на сцену
            three.scene.add(rowClone);
            three.lastRowPosition = rowClone.position.clone();

            this.updateHeightInfo();
            this.log(`Добавлен ряд #${index} (${this.selectedSize})`);

            return rowClone;
        },

        // Добавление нового ряда пользователем
        addRow() {
            this.updateHeightInfo();

            // Проверка на доступное пространство
            if (!this.canAddRow(this.selectedSize)) {
                const message = `Недостаточно места для добавления ящика размера ${this.selectedSize}. Осталось ${this.remainingHeight}мм.`;
                this.log("Нехватка места", { warning: message });

                Toastify({
                    text: message,
                    duration: 3000,
                    close: true,
                    gravity: "bottom",
                    position: "right",
                    style: { background: "red" },
                }).showToast();

                return;
            }

            // Проверка порядка размеров
            if (this.addedRows.length > 0) {
                const lastSize = this.addedRows[this.addedRows.length - 1].size;

                if (
                    (lastSize === "small" &&
                        ["medium", "large"].includes(this.selectedSize)) ||
                    (lastSize === "medium" && this.selectedSize === "large")
                ) {
                    const message = "Выберите ящик меньшего размера.";
                    this.log("Неверный размер", { warning: message });

                    Toastify({
                        text: message,
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "right",
                        style: { background: "red" },
                    }).showToast();

                    return;
                }
            }

            // Добавляем данные
            this.addedRows.push({
                size: this.selectedSize,
                color: this.selectedColor,
            });

            // Создаем ряд
            const row = this.addBox();

            return row;
        },

        // Удаление ряда
        removeRow(index) {
            if (index < 0 || index >= this.addedRows.length) {
                this.log(`Попытка удаления несуществующего ряда #${index}`, {
                    warning: "Индекс вне диапазона",
                });
                return;
            }

            this.log(`Удаление ряда #${index}`);

            // Находим объект ряда
            const rowName = `row_${index}`;
            const rowToRemove = three.scene.getObjectByName(rowName);

            if (rowToRemove) {
                // Анимация и удаление
                gsap.to(rowToRemove.position, {
                    x: rowToRemove.position.x + 2,
                    duration: 0.3,
                    onComplete: () => {
                        three.scene.remove(rowToRemove);

                        // Очистка ресурсов
                        rowToRemove.traverse((child) => {
                            if (child.isMesh) {
                                child.geometry?.dispose();
                                if (Array.isArray(child.material)) {
                                    child.material.forEach((m) => m.dispose());
                                } else {
                                    child.material?.dispose();
                                }
                            }
                        });

                        this.log(`Ряд #${index} удален`);
                    },
                });
            }

            // Удаляем из данных и перестраиваем
            this.addedRows.splice(index, 1);
            this.updateHeightInfo();
            this.rebuildRows();
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
