import * as THREE from "three";
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { OBJLoader } from "three/addons/loaders/OBJLoader.js";
import { MTLLoader } from "three/addons/loaders/MTLLoader.js";
import Toastify from "toastify-js";
import { gsap } from "gsap";

// Создаем фабрику для Three.js вне Alpine.js
const createThreeObjects = () => {
    return {
        scene: new THREE.Scene(),
        camera: null,
        renderer: null,
        controls: null,
        animationFrameId: null,
        objLoader: new OBJLoader(),
        mtlLoader: new MTLLoader(),
        originalRow: null, // Хранение оригинальной строки (row)
        lastRowPosition: new THREE.Vector3(0, 0, 0), // Хранение последней позиции строки
    };
};

export default () => {
    // Константы для высоты модели и размеров рядов (в мм)
    const SHELF_HEIGHT = 1515; // Высота модели shelf в мм
    const ROW_HEIGHTS = {
        small: 75 + 35, // V1 с учетом расстояния между рядами (110 мм)
        medium: 121 + 35, // V2 с учетом расстояния между рядами (156 мм)
        large: 143 + 35, // V3 с учетом расстояния между рядами (178 мм)
    };

    // Коэффициент масштабирования для преобразования мм в единицы Three.js
    // Этот коэффициент нужно подобрать экспериментально, исходя из реальных размеров модели
    const SCALE_FACTOR = 0.00095; // Примерный коэффициент (1 мм = 0.0009 единицы Three.js)

    // Объекты Three.js хранятся отдельно от Alpine.js state
    const three = createThreeObjects();

    // Модели для загрузки с опциональными настройками
    const modelsToLoad = [
        {
            name: "shelf",
            obj: "/assets/models/shelf.obj",
            mtl: "/assets/models/shelf.mtl",
            options: {
                scale: null, // null означает использовать оригинальный масштаб
                position: null, // null означает использовать оригинальную позицию
                rotation: null, // null означает использовать оригинальную ротацию
            },
        },
        {
            name: "row",
            obj: "/assets/models/row.obj",
            mtl: "/assets/models/row.mtl",
            options: {
                scale: null,
                position: {
                    x: 0,
                    y: 0.25,
                    z: -0.25,
                },
                rotation: null,
            },
        },
    ];

    return {
        isLoaded: false,
        loadedModels: 0,
        totalModels: modelsToLoad.length,
        progress: 0,
        error: null,
        selectedColor: "red",
        selectedSize: "small",
        addedRows: [],
        colors: ["red", "green", "blue", "yellow", "gray"],
        debugMode: false, // Флаг включения/отключения режима отладки
        debugInfo: {
            cameraPosition: { x: 0, y: 0, z: 0 },
            modelCount: 0,
            lastAction: "Инициализация",
            renderFrames: 0,
            memoryUsage: "N/A",
            errorCount: 0,
            loadTime: 0,
            sceneObjects: [],
            rowsPositions: [],
            warnings: [],
        },
        sizes: [
            {
                name: "V1",
                value: "small",
            },
            {
                name: "V2",
                value: "medium",
            },
            {
                name: "V3",
                value: "large",
            },
        ],

        // Свойства для отображения информации о высоте и доступности
        usedHeightPercent: 0,
        remainingHeight: SHELF_HEIGHT,
        usedHeight: 0,
        canAddSmallRow: true,
        canAddMediumRow: true,
        canAddLargeRow: true,
        maxSmallRowsToAdd: Math.floor(SHELF_HEIGHT / ROW_HEIGHTS.small),
        maxMediumRowsToAdd: Math.floor(SHELF_HEIGHT / ROW_HEIGHTS.medium),
        maxLargeRowsToAdd: Math.floor(SHELF_HEIGHT / ROW_HEIGHTS.large),

        // Обновление отладочной информации
        updateDebugInfo() {
            if (!this.debugMode) return;

            // Обновляем информацию о камере
            if (three.camera) {
                this.debugInfo.cameraPosition = {
                    x: three.camera.position.x.toFixed(3),
                    y: three.camera.position.y.toFixed(3),
                    z: three.camera.position.z.toFixed(3),
                };
            }

            // Информация о количестве объектов
            let objectCount = 0;
            if (three.scene) {
                three.scene.traverse((object) => {
                    objectCount++;
                });
                this.debugInfo.modelCount = objectCount;

                // Собираем информацию о всех объектах на сцене
                this.debugInfo.sceneObjects = [];
                three.scene.children.forEach((child) => {
                    if (child.name && child.name.startsWith("row_")) {
                        // Подробное логирование для рядов
                        this.debugInfo.sceneObjects.push({
                            name: child.name,
                            type: child.type,
                            visible: child.visible,
                            position: {
                                x: child.position.x.toFixed(3),
                                y: child.position.y.toFixed(3),
                                z: child.position.z.toFixed(3),
                            },
                            isRow: true,
                        });
                    } else {
                        this.debugInfo.sceneObjects.push({
                            name: child.name || "unnamed",
                            type: child.type,
                            visible: child.visible,
                            position: {
                                x: child.position.x.toFixed(3),
                                y: child.position.y.toFixed(3),
                                z: child.position.z.toFixed(3),
                            },
                        });
                    }
                });
            }

            // Информация о добавленных рядах
            this.debugInfo.rowsPositions = [];
            this.addedRows.forEach((row, index) => {
                const rowName = `row_${index}`;
                const rowObj = three.scene.getObjectByName(rowName);

                this.debugInfo.rowsPositions.push({
                    index,
                    size: row.size,
                    color: row.color,
                    name: rowName,
                    found: rowObj ? true : false,
                    position: rowObj
                        ? {
                              x: rowObj.position.x.toFixed(3),
                              y: rowObj.position.y.toFixed(3),
                              z: rowObj.position.z.toFixed(3),
                          }
                        : "not found",
                });
            });

            // Добавляем дополнительную проверку - ищем все объекты с именами row_*
            const allRowsOnScene = [];
            three.scene.traverse((obj) => {
                if (obj.name && obj.name.startsWith("row_")) {
                    allRowsOnScene.push({
                        name: obj.name,
                        position: {
                            x: obj.position.x.toFixed(3),
                            y: obj.position.y.toFixed(3),
                            z: obj.position.z.toFixed(3),
                        },
                    });
                }
            });
            this.debugInfo.allRowsOnScene = allRowsOnScene;

            // Информация об использовании памяти (если доступно)
            if (window.performance && window.performance.memory) {
                const memory = window.performance.memory;
                this.debugInfo.memoryUsage = `${Math.round(memory.usedJSHeapSize / 1048576)} MB / ${Math.round(memory.jsHeapSizeLimit / 1048576)} MB`;
            }

            // Увеличиваем счетчик кадров
            this.debugInfo.renderFrames++;
        },

        // Логирование действий с отладочной информацией
        logDebugAction(action, data = null) {
            if (!this.debugMode) return;

            const timestamp = new Date().toLocaleTimeString();
            this.debugInfo.lastAction = `${timestamp}: ${action}`;

            if (data && data.warning) {
                this.debugInfo.warnings.push({
                    time: timestamp,
                    message: data.warning,
                });

                // Ограничиваем количество предупреждений до 10
                if (this.debugInfo.warnings.length > 10) {
                    this.debugInfo.warnings.shift();
                }
            }

            console.log(`[DEBUG] ${action}`, data);
        },
        toggleDebugMode() {
            this.debugMode = !this.debugMode;
            this.logDebugAction(
                `Режим отладки ${this.debugMode ? "включен" : "выключен"}`,
            );
        },

        // Функция для преобразования мм в единицы Three.js
        mmToUnits(mm) {
            return mm * SCALE_FACTOR;
        },

        // Функция для преобразования единиц Three.js в мм
        unitsToMm(units) {
            return units / SCALE_FACTOR;
        },

        // Метод для обновления всех расчетных свойств
        updateHeightInfo() {
            // Рассчитываем занятую высоту
            let usedHeight = 0;
            this.addedRows.forEach((row) => {
                usedHeight += ROW_HEIGHTS[row.size];
            });

            // Устанавливаем значения свойств
            this.usedHeight = usedHeight;
            this.remainingHeight = SHELF_HEIGHT - usedHeight;
            this.usedHeightPercent = Math.min(
                100,
                Math.round((usedHeight / SHELF_HEIGHT) * 100),
            );

            // Обновляем возможность добавления рядов
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
        },

        // Проверка возможности добавления ряда
        canAddRow(size) {
            return this.remainingHeight >= ROW_HEIGHTS[size];
        },

        init() {
            const startTime = performance.now();

            const container = this.$refs.scene;

            // Установка правильной высоты контейнера сцены
            this.adjustSceneHeight();

            // Настройка сцены
            three.scene.background = null;

            // Настройка камеры
            three.camera = new THREE.PerspectiveCamera(
                45,
                container.clientWidth / container.clientHeight,
                0.1,
                2000,
            );
            three.camera.position.z = 1;

            // Настройка рендерера
            three.renderer = new THREE.WebGLRenderer({
                antialias: true,
                alpha: true,
            });
            three.renderer.setSize(
                container.clientWidth,
                container.clientHeight,
                false,
            );
            three.renderer.setPixelRatio(window.devicePixelRatio);
            three.renderer.shadowMap.enabled = true;
            container.appendChild(three.renderer.domElement);

            // Базовый рассеянный свет (минимальной интенсивности)
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.3);
            three.scene.add(ambientLight);

            // Основной направленный свет для создания ярких бликов
            const mainLight = new THREE.DirectionalLight(0xffffff, 1.0);
            mainLight.position.set(1, 4, 2);
            mainLight.castShadow = true;
            mainLight.shadow.mapSize.width = 2048;
            mainLight.shadow.mapSize.height = 2048;
            three.scene.add(mainLight);

            // Дополнительный точечный свет для создания выраженных бликов
            const spotLight = new THREE.SpotLight(
                0xffffff,
                0.8,
                100,
                Math.PI / 4,
                0.5,
                1,
            );
            spotLight.position.set(-2, 5, 4);
            spotLight.castShadow = true;
            three.scene.add(spotLight);

            // Контровой свет для подсветки краев и выделения формы металла
            const rimLight = new THREE.DirectionalLight(0xadd8e6, 0.6); // Слегка голубоватый для холодного металлического блеска
            rimLight.position.set(-3, 1, -5);
            three.scene.add(rimLight);

            // Заполняющий свет для смягчения теней без потери контраста
            const fillLight = new THREE.DirectionalLight(0xffffee, 0.4); // Слегка теплый тон
            fillLight.position.set(3, 0, 3);
            three.scene.add(fillLight);

            // Широкий мягкий источник для имитации окружающих отражений
            const envLight = new THREE.HemisphereLight(0xffffff, 0xcccccc, 0.3);
            three.scene.add(envLight);

            // Настройка управления
            three.controls = new OrbitControls(
                three.camera,
                three.renderer.domElement,
            );
            three.controls.enableDamping = true;
            three.controls.dampingFactor = 0.05;

            // Загрузка моделей
            this.loadModels();

            // Запуск рендеринга
            this.startRenderLoop();

            // Обновляем информацию о высоте
            this.updateHeightInfo();

            // Обработчик изменения размера
            window.addEventListener("resize", this.onWindowResize.bind(this));

            // После инициализации
            this.debugInfo.loadTime = (
                (performance.now() - startTime) /
                1000
            ).toFixed(2);
            this.logDebugAction("Инициализация завершена", {
                time: this.debugInfo.loadTime,
            });

            // Запускаем периодическое обновление отладочной информации
            setInterval(() => {
                this.updateDebugInfo();
            }, 1000);
        },

        // Функция для установки правильной высоты сцены
        adjustSceneHeight() {
            const container = this.$refs.scene;
            const projection = this.$refs.projection;
            if (!container) return;

            const windowHeight = window.innerHeight;
            const headerHeight =
                document.querySelector("header")?.offsetHeight || 0;
            const sceneHeight = windowHeight - headerHeight;

            // Устанавливаем высоту для контейнера сцены
            container.style.height = `${sceneHeight}px`;
            projection.style.height = `${sceneHeight}px`;

            // Если рендерер уже инициализирован, обновляем его размеры
            if (three.renderer && three.camera) {
                three.renderer.setSize(
                    container.clientWidth,
                    sceneHeight,
                    false,
                );
                three.camera.aspect = container.clientWidth / sceneHeight;
                three.camera.updateProjectionMatrix();
            }
        },

        // Функция для загрузки нескольких моделей
        loadModels() {
            // Загружаем все модели из массива
            modelsToLoad.forEach((model) => {
                this.loadModel(model);
            });
        },

        loadModel(model) {
            this.logDebugAction(`Загрузка модели ${model.name}`);

            const mtlLoader = new MTLLoader();

            mtlLoader.load(
                model.mtl,
                (materials) => {
                    materials.preload();

                    const objLoader = new OBJLoader();
                    objLoader.setMaterials(materials);

                    objLoader.load(
                        model.obj,
                        (object) => {
                            // Настройка теней для всех мешей модели
                            object.traverse(function (child) {
                                if (child.isMesh) {
                                    child.castShadow = true;
                                    child.receiveShadow = true;
                                    child.material.color.set("#ffffff");
                                }
                            });

                            // Применяем дополнительные настройки, если они указаны
                            this.applyModelOptions(object, model.options);
                            // Если это модель row, сохраняем её как оригинальную
                            if (model.name === "row") {
                                object.getObjectByName("box", true).position.y =
                                    0.3;
                                object.getObjectByName(
                                    "box_medium",
                                    true,
                                ).position.y = 0.3;
                                object.getObjectByName(
                                    "box_large",
                                    true,
                                ).position.y = 0.3;

                                object.visible = false;

                                object.getObjectByName("box", true).visible =
                                    false;
                                object.getObjectByName(
                                    "box_medium",
                                    true,
                                ).visible = false;
                                object.getObjectByName(
                                    "box_large",
                                    true,
                                ).visible = false;

                                three.originalRow = object.clone();
                            }

                            // Сохраняем ссылку на объект
                            model.object = object;

                            // Добавляем модель на сцену
                            three.scene.add(object);

                            // После загрузки всех моделей, настраиваем камеру, чтобы видеть все объекты
                            this.loadedModels++;
                            if (this.loadedModels === this.totalModels) {
                                this.isLoaded = true;
                                this.fitCameraToObjects();
                                // Обновляем информацию о высоте после загрузки всех моделей
                                this.updateHeightInfo();
                                this.logDebugAction("Все модели загружены", {
                                    totalModels: this.totalModels,
                                });
                            }
                        },
                        (xhr) => {
                            // Обновляем прогресс загрузки
                            const modelProgress =
                                (xhr.loaded / xhr.total) *
                                (100 / this.totalModels);
                            const modelIndex = modelsToLoad.indexOf(model);
                            const baseProgress =
                                modelIndex * (100 / this.totalModels);
                            this.progress = baseProgress + modelProgress;

                            if (xhr.loaded === xhr.total) {
                                this.logDebugAction(
                                    `Модель ${model.name} загружена`,
                                    {
                                        size: `${(xhr.total / 1024).toFixed(2)} KB`,
                                    },
                                );
                            }
                        },
                        (error) => {
                            this.error = `Error loading model ${model.name}`;
                            this.logDebugAction(
                                `Ошибка загрузки модели ${model.name}`,
                                { warning: error.message },
                            );
                            this.debugInfo.errorCount++;
                            console.error(error);
                        },
                    );
                },
                null,
                (error) => {
                    this.error = `Error loading materials for ${model.name}`;
                    this.logDebugAction(
                        `Ошибка загрузки материалов для ${model.name}`,
                        { warning: error.message },
                    );
                    this.debugInfo.errorCount++;
                    console.error(error);
                },
            );
        },

        // Функция для применения дополнительных настроек к модели
        applyModelOptions(object, options) {
            if (!options) return;

            // Применяем масштаб, если указан
            if (options.scale !== null) {
                if (typeof options.scale === "number") {
                    // Если указано одно число, применяем одинаковый масштаб по всем осям
                    object.scale.set(
                        options.scale,
                        options.scale,
                        options.scale,
                    );
                } else if (options.scale instanceof Object) {
                    // Если указан объект с x, y, z
                    if (options.scale.x !== undefined)
                        object.scale.x = options.scale.x;
                    if (options.scale.y !== undefined)
                        object.scale.y = options.scale.y;
                    if (options.scale.z !== undefined)
                        object.scale.z = options.scale.z;
                }
            }

            // Применяем позицию, если указана
            if (options.position !== null) {
                if (options.position instanceof Object) {
                    if (options.position.x !== undefined)
                        object.position.x = options.position.x;
                    if (options.position.y !== undefined)
                        object.position.y = options.position.y;
                    if (options.position.z !== undefined)
                        object.position.z = options.position.z;
                }
            }

            // Применяем вращение, если указано
            if (options.rotation !== null) {
                if (options.rotation instanceof Object) {
                    if (options.rotation.x !== undefined)
                        object.rotation.x = THREE.MathUtils.degToRad(
                            options.rotation.x,
                        );
                    if (options.rotation.y !== undefined)
                        object.rotation.y = THREE.MathUtils.degToRad(
                            options.rotation.y,
                        );
                    if (options.rotation.z !== undefined)
                        object.rotation.z = THREE.MathUtils.degToRad(
                            options.rotation.z,
                        );
                }
            }
        },

        // Функция для настройки камеры так, чтобы видеть все объекты
        fitCameraToObjects() {
            // Создаем общий бокс для всех объектов
            const box = new THREE.Box3();

            // Добавляем все модели в бокс
            modelsToLoad.forEach((model) => {
                if (model.object) {
                    box.expandByObject(model.object);
                }
            });

            const size = box.getSize(new THREE.Vector3());
            const center = box.getCenter(new THREE.Vector3());

            // Устанавливаем центр сцены как центр всех объектов
            three.controls.target.copy(center);

            // Устанавливаем камеру так, чтобы видеть все объекты
            const maxDim = Math.max(size.x, size.y, size.z);
            const fov = three.camera.fov * (Math.PI / 180);
            const cameraZ = Math.abs(maxDim / (2 * Math.tan(fov / 2)));

            // Устанавливаем позицию камеры
            const direction = new THREE.Vector3(0, 0, 1);
            direction.applyQuaternion(three.camera.quaternion);
            direction.multiplyScalar(cameraZ * 1.5); // Немного дальше, чтобы было видно все целиком

            three.camera.position.copy(center).add(direction);
            three.camera.updateProjectionMatrix();

            // Обновляем controls
            three.controls.update();
        },

        // Универсальная функция для добавления кубов
        addBox(
            selector = "box",
            count = 6,
            offset = 0.1,
            rowHeightMm = 110,
            customIndex = null,
        ) {
            // Проверяем наличие оригинальной строки
            if (!three.originalRow) {
                console.error("Original row model is not defined");
                return;
            }

            // Преобразуем высоту ряда из мм в единицы Three.js
            const rowHeightUnits = this.mmToUnits(rowHeightMm);
            // Клонируем оригинальную строку
            let rowClone = three.originalRow.clone();
            rowClone.visible = true;

            // Находим элемент 'box' в клоне строки
            let originalBox = rowClone.getObjectByName(selector, true);

            if (!originalBox) {
                console.error("Box element not found in row object");
                return;
            }

            // Клонируем box нужное количество раз и добавляем к rowClone
            for (let i = 0; i < count; i++) {
                let boxClone = originalBox.clone();
                boxClone.visible = true;

                // Создаем новые материалы для каждого клона
                if (boxClone.material) {
                    if (!Array.isArray(boxClone.material)) {
                        boxClone.material = boxClone.material.clone();
                        boxClone.material.color.set(this.selectedColor);
                    } else {
                        boxClone.material = boxClone.material.map((mat) => {
                            const newMat = mat.clone();
                            newMat.color.set(this.selectedColor);
                            return newMat;
                        });
                    }
                }

                // Устанавливаем смещение по оси X для каждого нового box
                boxClone.position.set(
                    originalBox.position.x + i * offset,
                    originalBox.position.y,
                    originalBox.position.z,
                );

                // Даем уникальное имя каждому клону
                boxClone.name = `box_clone_size${i}_${Math.random().toString(36).substr(2, 9)}`;

                // Добавляем клонированный box к rowClone
                rowClone.add(boxClone);
                gsap.to(
                    rowClone.getObjectByName(boxClone.name, true).position,
                    {
                        y: 0,
                        duration: 0.5,
                        delay: i * 0.02,
                        ease: "bounce.out",
                    },
                );
            }

            // Устанавливаем позицию нового клона с учетом высоты в единицах Three.js
            let yPosition = 0;
            if (this.addedRows.length > 0) {
                // Рассчитываем суммарную высоту всех предыдущих рядов
                let totalHeightMm = 0;
                for (let i = 0; i < this.addedRows.length; i++) {
                    totalHeightMm += ROW_HEIGHTS[this.addedRows[i].size];
                }
                // Преобразуем мм в единицы Three.js
                yPosition = this.mmToUnits(totalHeightMm);
            }

            rowClone.position.set(
                three.originalRow.position.x,
                yPosition,
                three.originalRow.position.z,
            );

            // Генерируем уникальное имя для нового клона row
            // Используем customIndex, если он передан, иначе вычисляем по длине массива
            const newRowIndex =
                customIndex !== null ? customIndex : this.addedRows.length - 1;
            const newRowName = `row_${newRowIndex}`;
            rowClone.name = newRowName;

            // Логируем имя созданного ряда для отладки
            if (this.debugMode) {
                console.log(
                    `[addBox] Создан ряд с именем: ${newRowName}`,
                    rowClone,
                );
            }

            // Сохраняем позицию и добавляем на сцену
            three.scene.add(rowClone);
            three.lastRowPosition = rowClone.position.clone();

            // Обновляем информацию о высоте
            this.updateHeightInfo();

            return rowClone;
        },

        // Метод для добавления ряда
        addRow() {
            // Обновляем информацию о высоте перед проверкой
            this.updateHeightInfo();

            // Проверяем, хватит ли места для нового ряда
            if (!this.canAddRow(this.selectedSize)) {
                const warning = `Недостаточно места для добавления ящика размера ${this.selectedSize}. Осталось ${this.remainingHeight}мм.`;
                this.logDebugAction("Попытка добавления ряда", { warning });

                Toastify({
                    text: `Недостаточно места для добавления ящика размера ${this.selectedSize}. Осталось ${this.remainingHeight}мм.`,
                    duration: 3000,
                    close: true,
                    gravity: "bottom",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, red, red)",
                    },
                }).showToast();
                return;
            }

            // Текущая проверка для small/medium/large размещения
            if (this.addedRows.length) {
                if (
                    (this.addedRows[this.addedRows.length - 1].size ==
                        "small" &&
                        (this.selectedSize == "medium" ||
                            this.selectedSize == "large")) ||
                    (this.addedRows[this.addedRows.length - 1].size ==
                        "medium" &&
                        this.selectedSize == "large")
                ) {
                    const warning = `Выберите ящик меньшего размера.`;
                    this.logDebugAction(
                        "Попытка добавления неправильного размера ряда",
                        { warning },
                    );

                    Toastify({
                        text: `Выберите ящик меньшего размера.`,
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, red, red)",
                        },
                        onClick: function () {},
                    }).showToast();

                    return;
                }
            }

            this.addedRows.push({
                size: this.selectedSize,
                color: this.selectedColor,
            });

            let addedRow;
            // Передаем точные размеры в мм для каждого типа ящика
            switch (this.selectedSize) {
                case "small":
                    addedRow = this.addBox("box", 6, -0.106, ROW_HEIGHTS.small);
                    break;
                case "medium":
                    addedRow = this.addBox(
                        "box_medium",
                        4,
                        -0.16,
                        ROW_HEIGHTS.medium,
                    );
                    break;
                case "large":
                    addedRow = this.addBox(
                        "box_large",
                        3,
                        -0.215,
                        ROW_HEIGHTS.large,
                    );
                    break;
                default:
                    addedRow = this.addBox("box", 6, -0.106, ROW_HEIGHTS.small);
                    break;
            }

            this.logDebugAction(
                `Добавлен ряд #${this.addedRows.length - 1} размера ${this.selectedSize}`,
                {
                    rowCount: this.addedRows.length,
                    remainingHeight: this.remainingHeight,
                    position: addedRow
                        ? {
                              x: addedRow.position.x.toFixed(3),
                              y: addedRow.position.y.toFixed(3),
                              z: addedRow.position.z.toFixed(3),
                          }
                        : "unknown",
                },
            );

            return addedRow;
        },

        // Метод для удаления ряда
        removeRow(index) {
            // Проверяем, существует ли строка с указанным индексом
            if (index < 0 || index >= this.addedRows.length) {
                const warning = `Попытка удалить несуществующую строку с индексом ${index}`;
                this.logDebugAction(warning, { warning });
                console.warn(warning);
                return;
            }

            this.logDebugAction(`Удаление ряда #${index}`, {
                rowData: this.addedRows[index],
            });

            // Находим и удаляем объект строки из сцены
            const rowName = `row_${index}`;
            const rowToRemove = three.scene.getObjectByName(rowName);

            if (rowToRemove) {
                // Анимация исчезновения перед удалением - используем смещение без opacity
                gsap.to(rowToRemove.position, {
                    x: rowToRemove.position.x + 2, // Сдвигаем в сторону
                    duration: 0.3,
                    onComplete: () => {
                        // Удаляем объект из сцены
                        three.scene.remove(rowToRemove);

                        // Очищаем ресурсы
                        rowToRemove.traverse((child) => {
                            if (child.isMesh) {
                                if (child.geometry) child.geometry.dispose();
                                if (child.material) {
                                    if (Array.isArray(child.material)) {
                                        child.material.forEach((material) =>
                                            material.dispose(),
                                        );
                                    } else {
                                        child.material.dispose();
                                    }
                                }
                            }
                        });

                        this.logDebugAction(
                            `Ряд #${index} удален и ресурсы очищены`,
                        );
                    },
                });
            } else {
                const warning = `Объект с именем ${rowName} не найден на сцене`;
                this.logDebugAction(warning, { warning });
                console.warn(warning);
            }

            // Удаляем данные о строке из массива
            this.addedRows.splice(index, 1);

            // Обновляем информацию о высоте после удаления
            this.updateHeightInfo();

            // Сбрасываем последнюю позицию строки
            three.lastRowPosition = new THREE.Vector3(0, 0, 0);

            // Перестраиваем все строки модели после удаления
            this.rebuildRows();
        },

        // Метод для перестройки всех строк модели с правильными позициями
        rebuildRows() {
            this.logDebugAction("Перестройка рядов", {
                rowCount: this.addedRows.length,
            });

            // Удаляем все существующие строки из сцены
            let removedCount = 0;
            for (let i = 0; i < this.addedRows.length + 1; i++) {
                // +1 для проверки на лишние ряды
                const rowName = `row_${i}`;
                const existingRow = three.scene.getObjectByName(rowName);
                if (existingRow) {
                    three.scene.remove(existingRow);
                    removedCount++;
                }
            }

            this.logDebugAction(
                `Удалено ${removedCount} рядов при перестройке`,
            );

            // Создаем копию массива для сохранения данных о строках
            const rowsData = JSON.parse(JSON.stringify(this.addedRows));

            // Очищаем массив строк для последующего добавления
            this.addedRows = [];

            // Сбрасываем последнюю позицию строки
            three.lastRowPosition = new THREE.Vector3(0, 0, 0);

            // Временно отключаем логирование для предотвращения спама
            const originalLogMethod = this.logDebugAction;
            this.logDebugAction = () => {}; // Временно заменяем на пустую функцию

            // Добавляем строки заново в правильном порядке
            rowsData.forEach((rowData, idx) => {
                // Устанавливаем параметры для текущей строки
                this.selectedSize = rowData.size;
                this.selectedColor = rowData.color;

                // Добавляем строку с явным указанием индекса
                switch (rowData.size) {
                    case "small":
                        this.addBox("box", 6, -0.106, ROW_HEIGHTS.small, idx);
                        break;
                    case "medium":
                        this.addBox(
                            "box_medium",
                            4,
                            -0.16,
                            ROW_HEIGHTS.medium,
                            idx,
                        );
                        break;
                    case "large":
                        this.addBox(
                            "box_large",
                            3,
                            -0.215,
                            ROW_HEIGHTS.large,
                            idx,
                        );
                        break;
                    default:
                        this.addBox("box", 6, -0.106, ROW_HEIGHTS.small, idx);
                        break;
                }

                // Также добавляем данные в массив addedRows
                this.addedRows.push(rowData);
            });

            // Восстанавливаем оригинальный метод логирования
            this.logDebugAction = originalLogMethod;

            // Проверяем, что все ряды существуют на сцене
            const missingRows = [];
            this.addedRows.forEach((row, idx) => {
                const rowObj = three.scene.getObjectByName(`row_${idx}`);
                if (!rowObj) {
                    missingRows.push(idx);
                }
            });

            if (missingRows.length > 0) {
                this.logDebugAction(
                    `Отсутствуют объекты для рядов: ${missingRows.join(", ")}`,
                    {
                        warning: `Не все ряды были правильно созданы при перестройке`,
                    },
                );
            }

            this.logDebugAction(
                `Добавлено ${this.addedRows.length} рядов при перестройке`,
            );

            // Обновляем информацию о высоте после перестройки модели
            this.updateHeightInfo();

            this.logDebugAction("Перестройка рядов завершена", {
                totalRows: this.addedRows.length,
                rowNames: this.addedRows.map((_, idx) => `row_${idx}`),
            });
        },

        selectColor(color) {
            this.selectedColor = color;
        },

        selectSize(size) {
            this.selectedSize = size;
        },

        startRenderLoop() {
            let frameCount = 0;
            let lastTime = performance.now();

            const animate = () => {
                three.animationFrameId = requestAnimationFrame(animate);

                // Обновляем контролы и рендерим сцену
                if (three.controls) three.controls.update();
                if (three.renderer && three.scene && three.camera) {
                    three.renderer.render(three.scene, three.camera);
                }

                // Измеряем FPS каждую секунду
                const now = performance.now();
                frameCount++;

                if (now - lastTime >= 1000) {
                    if (this.debugMode) {
                        this.debugInfo.fps = frameCount;
                    }
                    frameCount = 0;
                    lastTime = now;
                }
            };

            animate();
        },

        onWindowResize() {
            this.adjustSceneHeight();
        },

        cleanup() {
            if (three.animationFrameId) {
                cancelAnimationFrame(three.animationFrameId);
            }

            window.removeEventListener("resize", this.onWindowResize);

            if (three.renderer && this.$refs.scene) {
                this.$refs.scene.removeChild(three.renderer.domElement);
                three.renderer.dispose();
            }

            // Очищаем ссылки на объекты Three.js
            Object.keys(three).forEach((key) => {
                three[key] = null;
            });
        },
    };
};
