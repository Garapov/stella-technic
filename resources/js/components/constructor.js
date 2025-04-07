import * as THREE from "three";
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { OBJLoader } from "three/addons/loaders/OBJLoader.js";
import { MTLLoader } from "three/addons/loaders/MTLLoader.js";

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
                    y: 0,
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
        color: "#ff0000",

        init() {
            const container = this.$refs.scene;

            // Установка правильной высоты контейнера сцены
            this.adjustSceneHeight();

            // Настройка сцены
            three.scene.background = new THREE.Color(0xf0f0f0);

            // Настройка камеры
            three.camera = new THREE.PerspectiveCamera(
                45,
                container.clientWidth / container.clientHeight,
                0.1,
                2000,
            );
            three.camera.position.z = 100;

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

            // Освещение
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            three.scene.add(ambientLight);

            const mainLight = new THREE.DirectionalLight(0xffffff, 1.0);
            mainLight.position.set(2, 4, 5);
            mainLight.castShadow = true;
            three.scene.add(mainLight);

            const fillLight = new THREE.DirectionalLight(0xffffff, 0.3);
            fillLight.position.set(-5, 2, 3);
            three.scene.add(fillLight);

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

            // Обработчик изменения размера
            window.addEventListener("resize", this.onWindowResize.bind(this));
        },

        // Функция для установки правильной высоты сцены
        adjustSceneHeight() {
            const container = this.$refs.scene;
            if (!container) return;

            const windowHeight = window.innerHeight;
            const headerHeight =
                document.querySelector("header")?.offsetHeight || 0;
            const sceneHeight = windowHeight - headerHeight;

            // Устанавливаем высоту для контейнера сцены
            container.style.height = `${sceneHeight}px`;

            console.log(
                `Adjusting scene height: Window ${windowHeight}px, Header ${headerHeight}px, Scene ${sceneHeight}px`,
            );

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
                                }
                            });

                            // Применяем дополнительные настройки, если они указаны
                            this.applyModelOptions(object, model.options);

                            // Если это модель row, сохраняем её как оригинальную
                            if (model.name === "row") {
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
                                console.log(
                                    "Original row model saved",
                                    three.originalRow,
                                );
                            }

                            // Сохраняем ссылку на объект
                            model.object = object;

                            // Добавляем модель на сцену
                            three.scene.add(object);

                            // После загрузки всех моделей, настраиваем камеру, чтобы видеть все объекты
                            this.loadedModels++;
                            if (this.loadedModels === this.totalModels) {
                                this.isLoaded = true;
                                console.log("All models loaded");
                                this.fitCameraToObjects();
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
                        },
                        (error) => {
                            this.error = `Error loading model ${model.name}`;
                            console.error(error);
                        },
                    );
                },
                null,
                (error) => {
                    this.error = `Error loading materials for ${model.name}`;
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
        addBox(selector = "box", count = 6, offset = 0.1, rowOffset = 0.105) {
            // Проверяем наличие оригинальной строки
            if (!three.originalRow) {
                console.error("Original row model is not defined");
                return;
            }

            console.log(`Adding ${count} boxes with offset ${offset}`);

            // Клонируем оригинальную строку
            let rowClone = three.originalRow.clone();

            rowClone.visible = true;

            // Находим элемент 'box' в клоне строки
            let originalBox = rowClone.getObjectByName(selector, true);

            if (!originalBox) {
                console.error("Box element not found in row object");
                return;
            }

            console.log("Found original box:", originalBox);

            // Клонируем box нужное количество раз и добавляем к rowClone
            for (let i = 0; i < count; i++) {
                let boxClone = originalBox.clone();
                boxClone.visible = true;

                // Устанавливаем смещение по оси X для каждого нового box
                boxClone.position.set(
                    originalBox.position.x + i * offset,
                    originalBox.position.y,
                    originalBox.position.z,
                );

                // Даем уникальное имя каждому клону
                boxClone.name = `box_clone_size${i}_${Math.random().toString(36).substr(2, 9)}`;
                boxClone.material.color.set(this.color);
                // Добавляем клонированный box к rowClone
                rowClone.add(boxClone);
            }

            // Устанавливаем позицию нового клона с смещением по Y
            // Если это первый ряд, устанавливаем его в начальную позицию
            // Иначе смещаем от последней позиции

            let yPosition = 0;

            if (three.lastRowPosition) {
                yPosition = three.lastRowPosition.y + rowOffset;
            }

            rowClone.position.set(
                three.originalRow.position.x,
                yPosition,
                three.originalRow.position.z,
            );

            // Генерируем уникальное имя для нового клона row
            const newRowName = `row_clone_size${Math.random().toString(36).substr(2, 9)}`;
            rowClone.name = newRowName;

            // Добавляем на сцену клон row с добавленными клонами box
            three.scene.add(rowClone);

            console.log(`Added new row clone at position y: ${yPosition}`);

            // Сохраняем позицию последнего добавленного ряда
            three.lastRowPosition = rowClone.position.clone();

            return rowClone; // Возвращаем созданный клон для возможного использования
        },

        // Функции-обертки для удобства
        addSmallBox() {
            return this.addBox("box", 6, -0.106, 0.105);
        },

        addMediumBox() {
            return this.addBox("box_medium", 4, -0.16, 0.14);
        },

        addLargeBox() {
            return this.addBox("box_large", 3, -0.215, 0.165);
        },

        startRenderLoop() {
            const animate = () => {
                three.animationFrameId = requestAnimationFrame(animate);
                if (three.controls) three.controls.update();
                if (three.renderer && three.scene && three.camera) {
                    three.renderer.render(three.scene, three.camera);
                }
            };
            animate();
        },

        onWindowResize() {
            console.log("Window resized");
            // Используем функцию установки высоты сцены при изменении размера окна
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
