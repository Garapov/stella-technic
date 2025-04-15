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

    three.renderer.shadowMap.enabled = true;
    three.renderer.shadowMap.type = THREE.PCFSoftShadowMap; // default THREE.PCFShadowMap

    // Настройка освещения
    setupLights(three.scene);

    return three;
}

// Настройка освещения
export function setupLights(scene) {
    // Базовый свет
    scene.add(new THREE.AmbientLight(0xffffff, 1));

    // Направленный свет
    const mainLight = new THREE.DirectionalLight(0xffffff, 1.0);
    mainLight.castShadow = true;
    mainLight.shadow.radius = 0.1;
    mainLight.shadow.blurSamples = 1;
    mainLight.position.set(1, 2, 2);
    scene.add(mainLight);

    // const pointLight = new THREE.PointLight(0xff0000, 1, 100);
    // pointLight.position.set(50, 50, 50);
    // pointLight.castShadow = true;
    // pointLight.target = scene.getObjectByName("models");
    // scene.add(pointLight);

    // Дополнительное освещение для металлического эффекта
    // [
    //     {
    //         type: "spot",
    //         color: 0xffffff,
    //         intensity: 0.8,
    //         position: [-2, 5, 4],
    //     },
    //     {
    //         type: "directional",
    //         color: 0xadd8e6,
    //         intensity: 0.6,
    //         position: [-3, 1, -5],
    //     },
    //     {
    //         type: "directional",
    //         color: 0xffffee,
    //         intensity: 0.4,
    //         position: [3, 0, 3],
    //     },
    // ].forEach((light) => {
    //     const newLight =
    //         light.type === "spot"
    //             ? new THREE.SpotLight(
    //                   light.color,
    //                   light.intensity,
    //                   100,
    //                   Math.PI / 4,
    //                   0.5,
    //               )
    //             : new THREE.DirectionalLight(light.color, light.intensity);

    //     newLight.position.set(...light.position);
    //     newLight.castShadow = true;
    //     scene.add(newLight);
    // });

    // Полусферическое освещение
    scene.add(new THREE.HemisphereLight(0xffffff, 0xcccccc, 1));
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

export function createFloor(three) {
    const floorGeometry = new THREE.PlaneGeometry(10, 10); // Width and height of the plane
    const floorMaterial = new THREE.MeshStandardMaterial({
        color: 0xfefefe,
    }); // Color it gray for now
    const floor = new THREE.Mesh(floorGeometry, floorMaterial);

    // Rotate the floor to be horizontal (plane geometries are vertical by default)
    floor.rotation.x = Math.PI / -2;

    // Add shadow properties to the floor
    floor.receiveShadow = true;
    floor.name = "floor";

    three.scene.add(floor);
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
