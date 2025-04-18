import * as THREE from "three";
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { updateFPS } from "./debug-utils";

// Инициализация Three.js
export function setupThreeEnvironment(container) {
    // Контейнер для Three.js объектов
    const three = {
        scene: new THREE.Scene(),
        camera: null,
        renderer: null,
        controls: null,
        originalRow: null,
        lastRowPosition: new THREE.Vector3(0, 0, 0),
    };

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
    three.renderer.setSize(container.clientWidth, container.clientHeight);
    three.renderer.setPixelRatio(window.devicePixelRatio);
    container.appendChild(three.renderer.domElement);

    // Управление камерой
    three.controls = new OrbitControls(three.camera, three.renderer.domElement);
    three.controls.enableDamping = true;
    three.controls.dampingFactor = 0.05;
    three.controls.enablePan = false;

    three.renderer.shadowMap.enabled = true;
    three.renderer.shadowMap.type = THREE.PCFSoftShadowMap; // default THREE.PCFShadowMap

    // Настройка освещения
    setupLights(three.scene);

    return three;
}

// Настройка освещения
export function setupLights(scene) {
    // Базовый свет
    scene.add(new THREE.AmbientLight(0xd3d3d3, 1));

    // Направленный свет
    const mainLight = new THREE.DirectionalLight(0xffffb2, 1);
    mainLight.castShadow = true;
    mainLight.shadow.mapSize.width = 2048; // Default is 512
    mainLight.shadow.mapSize.height = 2048; // Default is 512
    mainLight.shadow.radius = 0;
    mainLight.shadow.blurSamples = 0;
    mainLight.position.set(1, 2, 1);
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
    three.camera.position.copy(center).add(direction.multiplyScalar(distance));
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
        "/assets/models/wood_seamless.jpg",
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
    const loader = new THREE.TextureLoader();

    const texture = loader.load(
        "/assets/models/dirty_concrete_wall_seamless.jpg",
        function (texture) {
            texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
            texture.offset.set(0, 0);
            texture.repeat.set(3, 3);
        },
    );

    const material = new THREE.MeshPhongMaterial({
        color: 0xffffff,
        specular: 0xffffff,
        shininess: 10,
        map: texture,
    });

    const floor = new THREE.Mesh(new THREE.PlaneGeometry(3, 3), material);
    floor.position.x = 0.35;
    floor.receiveShadow = true;

    // Rotate the floor to be horizontal (plane geometries are vertical by default)
    floor.rotation.x = Math.PI / -2;

    // Add shadow properties to the floor
    floor.receiveShadow = true;
    floor.castShadow = true;
    floor.name = "floor";

    three.scene.add(floor);
    createWall(three, [0.35, 1.1, -1.5], 3, 2.2, 0);
    createWall(three, [0.35, 1.1, 1.5], 3, 2.2, Math.PI);
    createWall(three, [-1.15, 1.1, 0], 3, 2.2, Math.PI / 2);
    createWall(three, [1.85, 1.1, 0], 3, 2.2, Math.PI / -2);
}

// Запуск цикла рендеринга
export function startRenderLoop(three, debugMode) {
    const animate = () => {
        requestAnimationFrame(animate);

        // Вызываем обновление FPS в каждом кадре, если включен режим отладки
        if (debugMode) {
            updateFPS();
        }

        if (three.controls) three.controls.update();
        if (three.renderer && three.scene && three.camera) {
            three.renderer.render(three.scene, three.camera);
        }
    };

    animate();
}
