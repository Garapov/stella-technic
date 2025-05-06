import * as THREE from "three";
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { updateFPS } from "./debug-utils";
import { updateRaycasting } from "./ui-manager.js";

let three = {};

// Инициализация Three.js
export function setupThreeEnvironment(container, projection, settings) {
    // Контейнер для Three.js объектов
    three = {
        scene: new THREE.Scene(),
        container: container,
        projection: projection,
        camera: null,
        cameraRTTProjection: null,
        renderer: null,
        renderer_for_projection: null,
        controls: null,
        controlsRTT: null,
        originalRow: null,
        lastRowPosition: new THREE.Vector3(0, 0, 0),
        settings: settings,
        raycaster: new THREE.Raycaster(),
        raycasterProjection: new THREE.Raycaster(), // Второй raycaster для проекционной камеры
        mouse: new THREE.Vector2(),
        mouseProjection: new THREE.Vector2(), // Координаты мыши для проекционного окна
        mouseClick: false,
        mouseClickProjection: false, // Клик в проекционном окне
        selectedObject: null,
        objectsToTest: [],
        isInteractingWithUI: false, // Флаг для отслеживания взаимодействия с UI
        mouseDownPosition: { x: 0, y: 0, view: null }, // Позиция при mousedown
    };
    // Обработчик событий мыши для основного окна
    window.addEventListener("mousemove", (event) => {
        // Определяем, над каким canvas находится мышь
        const rectMain = container.getBoundingClientRect();
        const rectProj = projection.getBoundingClientRect();

        // Проверяем, находится ли мышь над основным окном
        if (
            event.clientX >= rectMain.left &&
            event.clientX <= rectMain.right &&
            event.clientY >= rectMain.top &&
            event.clientY <= rectMain.bottom
        ) {
            // Рассчитываем координаты для основного окна
            three.mouse.x =
                ((event.clientX - rectMain.left) / rectMain.width) * 2 - 1;
            three.mouse.y =
                -((event.clientY - rectMain.top) / rectMain.height) * 2 + 1;
        }

        // Проверяем, находится ли мышь над проекционным окном
        if (
            event.clientX >= rectProj.left &&
            event.clientX <= rectProj.right &&
            event.clientY >= rectProj.top &&
            event.clientY <= rectProj.bottom
        ) {
            // Рассчитываем координаты для проекционного окна
            three.mouseProjection.x =
                ((event.clientX - rectProj.left) / rectProj.width) * 2 - 1;
            three.mouseProjection.y =
                -((event.clientY - rectProj.top) / rectProj.height) * 2 + 1;
        }
    });

    // Настройка камеры
    three.camera = new THREE.PerspectiveCamera(
        45,
        container.clientWidth / container.clientHeight,
        0.1,
        2000,
    );
    three.camera.position.z = 1;

    // Настройка ортографической камеры для проекции (вид спереди)
    three.cameraRTTProjection = new THREE.OrthographicCamera(
        projection.clientWidth / -2,
        projection.clientWidth / 2,
        projection.clientHeight / 2,
        projection.clientHeight / -2,
        -10000,
        10000,
    );

    // Позиционируем проекционную камеру для вида спереди
    three.cameraRTTProjection.position.set(0, 0, 5);
    three.cameraRTTProjection.lookAt(0, 0, 0);

    // Настройка рендереров - сначала инициализируем рендереры
    three.renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true,
    });
    three.renderer.setSize(container.clientWidth, container.clientHeight);
    three.renderer.setPixelRatio(window.devicePixelRatio);

    // Обработчики событий для основного view
    container.addEventListener("mousedown", (event) => {
        setMouseClocked(three, "container", true);
    });

    container.addEventListener("mouseup", (event) => {
        setMouseClocked(three, "container", false);
    });

    container.appendChild(three.renderer.domElement);

    three.renderer_for_projection = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true,
    });
    three.renderer_for_projection.setSize(
        projection.clientWidth,
        projection.clientHeight,
    );
    three.renderer_for_projection.setPixelRatio(window.devicePixelRatio);

    // Обработчики событий для проекционного view
    projection.addEventListener("mousedown", (event) => {
        setMouseClocked(three, "projections", true);
    });

    projection.addEventListener("mouseup", (event) => {
        setMouseClocked(three, "projections", false);
    });

    projection.appendChild(three.renderer_for_projection.domElement);

    // Теперь, когда рендереры готовы, настроим контроллеры
    three.controls = new OrbitControls(three.camera, three.renderer.domElement);
    three.controls.enableDamping = true;
    three.controls.dampingFactor = 0.05;
    three.controls.enablePan = false;
    three.controls.enableZoom = true;

    three.controlsRTT = new OrbitControls(
        three.cameraRTTProjection,
        three.renderer_for_projection.domElement,
    );
    three.controlsRTT.enableRotate = false;
    three.controlsRTT.enableZoom = false;
    three.controlsRTT.enablePan = false;
    three.controlsRTT.enableDamping = false;
    // three.controlsRTT.dampingFactor = 0.05;

    // Настройки рендерера
    three.renderer.shadowMap.enabled = true;
    three.renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    // Создаем визуализацию лучей для обеих камер
    // Луч для основной камеры
    const rayLineMaterial = new THREE.LineBasicMaterial({
        color: 0xff0000,
        transparent: true,
        opacity: 0.75,
    });

    const rayLineGeometry = new THREE.BufferGeometry();
    const points = [new THREE.Vector3(0, 0, 0), new THREE.Vector3(0, 0, 0)];
    rayLineGeometry.setFromPoints(points);

    const rayLine = new THREE.Line(rayLineGeometry, rayLineMaterial);
    rayLine.visible = false;
    three.scene.add(rayLine);
    three.rayLine = rayLine;

    // Луч для проекционной камеры
    const rayLineProjMaterial = new THREE.LineBasicMaterial({
        color: 0x00ffff, // Другой цвет для различия
        transparent: true,
        opacity: 0.75,
    });

    const rayLineProjGeometry = new THREE.BufferGeometry();
    rayLineProjGeometry.setFromPoints(points);

    const rayLineProj = new THREE.Line(
        rayLineProjGeometry,
        rayLineProjMaterial,
    );
    rayLineProj.visible = false;
    three.scene.add(rayLineProj);
    three.rayLineProj = rayLineProj;

    // Сфера для визуализации точки пересечения основного луча
    const intersectionSphere = new THREE.Mesh(
        new THREE.SphereGeometry(0.02, 16, 16),
        new THREE.MeshBasicMaterial({ color: 0xff0000 }),
    );
    intersectionSphere.visible = false;
    three.scene.add(intersectionSphere);
    three.intersectionSphere = intersectionSphere;

    // Сфера для визуализации точки пересечения проекционного луча
    const intersectionSphereProj = new THREE.Mesh(
        new THREE.SphereGeometry(0.02, 16, 16),
        new THREE.MeshBasicMaterial({ color: 0x00ffff }),
    );
    intersectionSphereProj.visible = false;
    three.scene.add(intersectionSphereProj);
    three.intersectionSphereProj = intersectionSphereProj;

    // Добавляем обработчик клавиш для переключения видимости лучей
    window.addEventListener("keydown", (event) => {
        if (event.key === "r" || event.key === "R") {
            three.rayLine.visible = !three.rayLine.visible;
            three.rayLineProj.visible = !three.rayLineProj.visible;

            if (!three.rayLine.visible) {
                three.intersectionSphere.visible = false;
                three.intersectionSphereProj.visible = false;
            }
        }
    });

    // Настройка освещения
    setupLights(three.scene);

    return three;
}

export function setMouseClocked(three, type, state) {
    if (type == "container") three.mouseClick = state;
    if (type == "projections") three.mouseClickProjection = state;
}

export function updateProjectionCamera(three) {
    // Проверяем, инициализированы ли необходимые компоненты
    if (!three.cameraRTTProjection || !three.controlsRTT) {
        console.warn(
            "Projection camera or controls not initialized yet. Skipping update.",
        );
        return;
    }

    // Находим объект 'models' на сцене
    const modelsObject = three.scene.getObjectByName("shelf", true);

    if (modelsObject) {
        // Создаем bounding box для объекта shelf
        const box = new THREE.Box3().setFromObject(modelsObject);
        const size = box.getSize(new THREE.Vector3());
        const center = box.getCenter(new THREE.Vector3());

        // Устанавливаем центр проекции на центр объекта
        three.controlsRTT.target.copy(center);

        // Получаем соотношение сторон контейнера проекции
        const containerAspect =
            three.projection.clientWidth / three.projection.clientHeight;

        // Получаем соотношение сторон объекта (ширина/высота)
        const objectAspect = size.x / size.y;

        let width, height;

        // Определяем, какая сторона ограничивает отображение
        if (objectAspect > containerAspect) {
            // Объект шире относительно контейнера - ограничиваем по ширине
            width = size.x * 1.1; // Добавляем небольшой отступ (10%)
            height = width / containerAspect;
        } else {
            // Объект выше относительно контейнера - ограничиваем по высоте
            height = size.y * 1.1; // Добавляем небольшой отступ (10%)
            width = height * containerAspect;
        }

        // Обновляем параметры ортографической камеры
        three.cameraRTTProjection.left = -width / 2;
        three.cameraRTTProjection.right = width / 2;
        three.cameraRTTProjection.top = height / 2;
        three.cameraRTTProjection.bottom = -height / 2;

        // Устанавливаем позицию камеры перед объектом
        const distance = Math.max(size.z * 0.5, 1); // минимальное расстояние не меньше 1
        three.cameraRTTProjection.position.z = center.z + distance;
        three.cameraRTTProjection.position.x = center.x;
        three.cameraRTTProjection.position.y = center.y;
        three.cameraRTTProjection.lookAt(center);

        // Обновляем проекционную матрицу камеры
        three.cameraRTTProjection.updateProjectionMatrix();
        three.controlsRTT.update();
    }
}

// Настройка освещения
export function setupLights(scene) {
    // Базовый свет
    scene.add(new THREE.AmbientLight(0xd3d3d3, 1));

    // Направленный свет
    const mainLight = new THREE.DirectionalLight(0xffffff, 1);
    mainLight.castShadow = true;
    mainLight.shadow.mapSize.width = 2048; // Default is 512
    mainLight.shadow.mapSize.height = 2048; // Default is 512
    mainLight.shadow.radius = 0;
    mainLight.shadow.blurSamples = 0;
    mainLight.position.set(0.1, 0.5, 0.5);
    scene.add(mainLight);

    // const mainLightHelper = new THREE.DirectionalLightHelper( mainLight, 5 );
    // scene.add( mainLightHelper );

    // const pointLight = new THREE.PointLight(0xff0000, 1, 100);
    // pointLight.position.set(0, 7, -2);
    // pointLight.castShadow = true;
    // pointLight.target = scene.getObjectByName("models");
    // scene.add(pointLight);

    // Дополнительное освещение для металлического эффекта
    [
        {
            type: "spot",
            color: 0xffffff,
            intensity: 0.8,
            position: [-2, 5, 4],
            shadow: false,
        },
        {
            type: "directional",
            color: 0xadd8e6,
            intensity: 0.6,
            position: [-3, 3, -5],
            shadow: false,
        },
        {
            type: "directional",
            color: 0xffffee,
            intensity: 0.4,
            position: [3, 4, 3],
            shadow: false,
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
                : new THREE.DirectionalLight(light.color, light.intensity);

        newLight.position.set(...light.position);
        newLight.castShadow = light.shadow;

        if (light.shadow) {
            newLight.shadow.radius = 0.02;
            newLight.shadow.blurSamples = 0;
        }

        scene.add(newLight);

        // const newLightHelper = light.type === "spot" ? new THREE.SpotLightHelper( newLight, 3 ) :  new THREE.DirectionalLightHelper( newLight, 3 )
        // scene.add( newLightHelper );
    });

    // Полусферическое освещение
    const sfereLight = new THREE.HemisphereLight(0xffffff, 0xffffff, 0.7);
    // sfereLight.castShadow = true;

    scene.add(sfereLight);
}

// Подгонка камеры к объектам
export function fitCameraToObjects(three) {
    // Проверяем, инициализированы ли controls
    if (!three.controls) {
        console.warn("Controls not initialized yet. Skipping camera fit.");
        return;
    }

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
    center.y += 0.8;
    center.z += 1.8;
    console.log('center', center);

    const maxDim = Math.max(size.x, size.y, size.z);
    const fov = three.camera.fov * (Math.PI / 180);
    const distance = Math.abs(maxDim / (2 * Math.tan(fov / 2))) * 2;

    const direction = new THREE.Vector3(0, 0, 1).applyQuaternion(
        three.camera.quaternion,
    );
    three.camera.position.copy(center).add(direction.multiplyScalar(distance));

    // Обновляем также проекционную камеру
    updateProjectionCamera(three);

    three.camera.updateProjectionMatrix();
    three.controls.update();
}

export function createWall(
    three,
    position = [0, 0, 0],
    sizeX = 3,
    sizeY = 3,
    rotation = 0,
) {
    const loader = new THREE.TextureLoader();

    const texture = loader.load(
        "/assets/models/concrete_bare_clean_seamless.jpg",
        function (texture) {
            texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
            texture.offset.set(0, 0);
            texture.repeat.set(3, 3);
        },
    );

    const material = new THREE.MeshPhongMaterial({
        color: 0xffffff,
        specular: 0xffffff,
        shininess: 0,
        map: texture,
    });

    const wall = new THREE.Mesh(
        new THREE.PlaneGeometry(sizeX, sizeY),
        material,
    );
    wall.position.x = position[0];
    wall.position.y = position[1];
    wall.position.z = position[2];

    // Rotate the wall to be horizontal (plane geometries are vertical by default)
    wall.rotation.y = rotation;

    // Add shadow properties to the wall
    wall.receiveShadow = true;
    wall.castShadow = true;
    wall.name = "wall";

    three.scene.add(wall);
}

export function createFloor(three) {
    // const loader = new THREE.TextureLoader();

    // const texture = loader.load(
    //     "/assets/models/dirty_concrete_wall_seamless.jpg",
    //     function (texture) {
    //         texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
    //         texture.offset.set(0, 0);
    //         texture.repeat.set(3, 3);
    //     },
    // );

    // const material = new THREE.MeshPhongMaterial({
    //     color: 0xffffff,
    //     specular: 0xffffff,
    //     shininess: 10,
    //     map: texture,
    // });

    // const floor = new THREE.Mesh(new THREE.PlaneGeometry(3, 3), material);
    // floor.position.x = 0.35;
    // floor.receiveShadow = true;

    // Rotate the floor to be horizontal (plane geometries are vertical by default)
    // floor.rotation.x = Math.PI / -2;

    // // Add shadow properties to the floor
    // floor.receiveShadow = true;
    // floor.castShadow = true;
    // floor.name = "floor";

    // three.scene.add(floor);
    createWall(three, [0.35, 0, -1.5], 9, 9, 0);
    createWall(three, [0.35, 0, 1.5], 9, 9, Math.PI);
    createWall(three, [-2.15, 0, 0], 9, 9, Math.PI / 2);
    createWall(three, [2.85, 0, 0], 9, 9, Math.PI / -2);
}

// Запуск цикла рендеринга
export function startRenderLoop(debugMode) {
    const animate = () => {
        requestAnimationFrame(animate);

        try {
            // Обновляем рейкастинг и UI
            updateRaycasting(three);

            // Вызываем обновление FPS в каждом кадре, если включен режим отладки
            if (debugMode) {
                updateFPS();
            }

            // Обновляем проекцию при каждом кадре (если controls инициализированы)
            if (three.controlsRTT) {
                try {
                    updateProjectionCamera(three);
                } catch (error) {
                    console.error("Error updating projection camera:", error);
                }
            }

            // Обновляем контролы и рендерим сцену с проверками
            if (three.controls) three.controls.update();

            if (three.renderer && three.scene && three.camera) {
                three.renderer.render(three.scene, three.camera);
            }

            if (
                three.renderer_for_projection &&
                three.scene &&
                three.cameraRTTProjection
            ) {
                three.renderer_for_projection.render(
                    three.scene,
                    three.cameraRTTProjection,
                );
            }
        } catch (error) {
            console.error("Error in animation loop:", error);
        }
    };

    animate();
}
