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

    // three.renderer.shadowMap.enabled = false;
    // three.renderer.shadowMap.type = THREE.PCFSoftShadowMap; // default THREE.PCFShadowMap

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
    mainLight.castShadow = false;
    // mainLight.shadow.radius = 0.1;
    // mainLight.shadow.blurSamples = 1;
    mainLight.position.set(1, 2, 2);
    scene.add(mainLight);

    // const pointLight = new THREE.PointLight(0xff0000, 1, 100);
    // pointLight.position.set(0, 6, -2);
    // // pointLight.castShadow = true;
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
    //     // newLight.castShadow = true;
    //     scene.add(newLight);
    // });

    // Полусферическое освещение
    scene.add(new THREE.HemisphereLight(0xffffff, 0xffffff, 1));
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

export function createWall(three, position = [0, 0, 0], sizeX = 3, sizeY = 3, rotation = 0) {
    const vertexShader = `
        varying vec2 vUv;

        void main() {
            vUv = uv;
            gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
        }
    `;

    const fragmentShader = `
        varying vec2 vUv;

        uniform vec3 brickColor1;      // Первый цвет кирпичей
        uniform vec3 brickColor2;      // Второй цвет кирпичей
        uniform vec3 mortarColor;      // Цвет швов
        uniform float brickWidth;      // Ширина кирпича
        uniform float brickHeight;     // Высота кирпича
        uniform float mortarThickness; // Толщина швов

        float random(vec2 st) {
            return fract(sin(dot(st.xy, vec2(12.9898, 78.233))) * 43758.5453);
        }

        void main() {
            // Определение смещения для четных рядов
            float rowOffset = step(0.5, mod(floor(vUv.y / (brickHeight + mortarThickness)), 2.0)) * brickWidth * 0.5;

            // Вычисление координат кирпича с учетом смещения и швов
            vec2 brickCoord = mod(vUv + vec2(rowOffset, 0.0), vec2(brickWidth, brickHeight + mortarThickness));

            // Проверка, является ли текущая область швом
            bool isMortar = brickCoord.y >= brickHeight || brickCoord.x < mortarThickness || brickCoord.x > brickWidth - mortarThickness;

            // Случайная вариация цвета кирпичей
            float variation = random(floor(vUv / vec2(brickWidth, brickHeight)));
            vec3 brickColor = mix(brickColor1, brickColor2, variation);

            // Задание финального цвета
            vec3 finalColor = isMortar ? mortarColor : brickColor;

            gl_FragColor = vec4(finalColor, 1.0);
        }
    `;

    // Использование в Three.js
    const material = new THREE.ShaderMaterial({
        vertexShader: vertexShader,
        fragmentShader: fragmentShader,
        uniforms: {
            brickColor1: { value: new THREE.Color(0.9, 0.9, 0.9) }, // Основной цвет кирпичей
            brickColor2: { value: new THREE.Color(0.8, 0.8, 0.8) }, // Второй цвет кирпичей
            mortarColor: { value: new THREE.Color(0.6, 0.6, 0.6) }, // Цвет швов
            brickWidth: { value: 0.09 },
            brickHeight: { value: 0.06 },
            mortarThickness: { value: 0.003 }
        }
    });

    const wall = new THREE.Mesh(new THREE.PlaneGeometry(3, 3), material);
    wall.position.x = position[0];
    wall.position.y = position[1];
    wall.position.z = position[2];

    // Rotate the wall to be horizontal (plane geometries are vertical by default)
    wall.rotation.y = rotation;

    // Add shadow properties to the wall
    wall.receiveShadow = true;
    wall.name = "wall";

    three.scene.add(wall);
}

export function createFloor(three) {
    const vertexShader = `
    varying vec2 vUv;
    varying vec3 vNormal;
    varying vec3 vPosition;

    void main() {
        vUv = uv;
        vNormal = normalize(normalMatrix * normal);
        vPosition = vec3(modelViewMatrix * vec4(position, 1.0));
        gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
    }
    `;

    const fragmentShader = `
    varying vec2 vUv;
    varying vec3 vNormal;
    varying vec3 vPosition;

    uniform vec3 lightDirection; // Направление света
    uniform vec3 lightColor;     // Цвет света
    uniform vec3 ambientColor;   // Цвет окружающего освещения

    void main() {
        float scale = 10.0; // Масштаб клеток
        vec2 coord = floor(vUv * scale);
        float checker = mod(coord.x + coord.y, 2.0);

        vec3 baseColor = mix(vec3(1.0), vec3(0.7, 0.7, 0.7), checker);

        // Освещение
        vec3 normal = normalize(vNormal);
        float lightIntensity = max(dot(normal, -lightDirection), 0.0);
        vec3 diffuse = lightColor * baseColor * lightIntensity;
        vec3 ambient = ambientColor * baseColor;

        gl_FragColor = vec4(diffuse + ambient, 1.0);
    }
    `;

    // Использование материала
    const material = new THREE.ShaderMaterial({
        vertexShader,
        fragmentShader,
        uniforms: {
            lightDirection: { value: new THREE.Vector3(0, -1, -1).normalize() },
            lightColor: { value: new THREE.Color(1, 1, 1) },
            ambientColor: { value: new THREE.Color(0.3, 0.3, 0.3) }
        }
    });

    const floor = new THREE.Mesh(new THREE.PlaneGeometry(3, 3), material);
    floor.position.x = 0.35;
    floor.receiveShadow = true;

    // Rotate the floor to be horizontal (plane geometries are vertical by default)
    floor.rotation.x = Math.PI / -2;

    // Add shadow properties to the floor
    floor.receiveShadow = true;
    floor.name = "floor";

    three.scene.add(floor);
    createWall(three, [0.35, 1.5, -1.5], 3, 3, 0);
    createWall(three, [0.35, 1.5, 1.5], 3, 3, Math.PI);
    createWall(three, [-1.15, 1.5, 0], 3, 3, Math.PI / 2);
    createWall(three, [1.85, 1.5, 0], 3, 3, Math.PI / -2);
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
