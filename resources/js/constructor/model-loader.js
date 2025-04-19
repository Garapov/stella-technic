import * as THREE from "three";
import { OBJLoader } from "three/addons/loaders/OBJLoader.js";
import { MTLLoader } from "three/addons/loaders/MTLLoader.js";
import { rotate } from "three/src/nodes/TSL.js";

import { HELPER_BOX_SELECTOR } from "./constants";

/**
 * Загружает все 3D модели
 * @param {Object} three Объект Three.js контейнера
 * @param {Array} models Массив моделей для загрузки
 * @param {Function} logCallback Функция для логирования
 * @param {Function} progressCallback Функция для обновления прогресса
 * @returns {Promise<boolean>} Статус загрузки
 */
export async function loadModels(three, models, logCallback, progressCallback) {
    const group = new THREE.Group();
    group.name = "models";
    three.scene.add(group);
    // Загрузка всех моделей параллельно
    await Promise.all(
        models.map((model, index) => {
            return loadModel(
                three,
                model,
                // Для логирования сообщений
                (message, data) => logCallback(message, data),
                // Для обновления прогресса загрузки с учетом общего прогресса
                (modelProgress) => {
                    // Рассчитываем общий прогресс с учетом вклада каждой модели
                    const totalProgress =
                        models.reduce((acc, _, i) => {
                            // Модель которая сейчас загружается имеет текущий прогресс,
                            // остальные модели либо 0% (еще не загружены), либо 100% (уже загружены)
                            return (
                                acc +
                                (i === index
                                    ? modelProgress
                                    : i < index
                                      ? 100
                                      : 0)
                            );
                        }, 0) / models.length;

                    if (progressCallback) {
                        progressCallback(totalProgress);
                    }
                },
            ).then((object) => {
                group.add(object);
            });
        }),
    );

    logCallback("Все модели загружены");
    return true;
}

/**
 * Загружает одну 3D модель
 * @param {Object} three Объект Three.js контейнера
 * @param {Object} model Данные модели для загрузки
 * @param {Function} logCallback Функция для логирования
 * @param {Function} progressCallback Функция для обновления прогресса
 * @returns {Promise<Object>} Загруженный объект
 */
async function loadModel(three, model, logCallback, progressCallback) {
    logCallback(`Загрузка модели ${model.name}`);

    // Загрузка материалов
    // const materials = await new Promise((resolve, reject) => {
    //     new MTLLoader().load(
    //         model.mtl,
    //         (materials) => resolve(materials),
    //         // Прогресс загрузки материалов
    //         (xhr) => {
    //             if (xhr.lengthComputable) {
    //                 const progress = (xhr.loaded / xhr.total) * 50; // Материалы - 50% общего прогресса
    //                 if (progressCallback) progressCallback(progress);
    //             }
    //         },
    //         (error) =>
    //             reject(
    //                 new Error(
    //                     `Ошибка загрузки материалов для ${model.name}: ${error.message}`,
    //                 ),
    //             ),
    //     );
    // });

    // materials.preload();
    const material = new THREE.MeshPhongMaterial({
        color: 0xd0f0f0,
        shininess: 100,
    });
    // Загрузка объекта
    const object = await new Promise((resolve, reject) => {
        const objLoader = new OBJLoader();
        // objLoader.setMaterials(materials);
        objLoader.load(
            model.obj,
            (object) => resolve(object),
            (xhr) => {
                // Прогресс загрузки объекта (начинаем с 50%, поскольку материалы уже загружены)
                if (xhr.lengthComputable) {
                    const progress = 50 + (xhr.loaded / xhr.total) * 50;
                    if (progressCallback) progressCallback(progress);
                }
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
            child.castShadow = true;
            child.receiveShadow = true;
        }
    });
    object.material = material;
    object.name = model.name;

    // Применение позиции
    if (model.position) {
        const { x = 0, y = 0, z = 0 } = model.position;
        object.position.set(x, y, z);
    }

    // Добавление на сцену
    three.scene.add(object);
    if (object.name == "shelf") {
        setupRotationPointsForHooks(three, object);
        addBoxForHeightCalculation(three, object);
    }
    model.object = object;

    // Сообщаем о 100% прогрессе
    if (progressCallback) progressCallback(100);

    return object;
}

export function addBoxForHeightCalculation(three, object) {
    // Проверим, что бокса еще нет
    let existingBox = three.scene.getObjectByName(HELPER_BOX_SELECTOR);

    if (existingBox) {
        console.log(
            `Бокс ${HELPER_BOX_SELECTOR} уже существует, обновляем его размеры`,
        );
        return updateHeightCalculationBox(
            three,
            object,
            HELPER_BOX_SELECTOR,
            false,
        );
    }

    // Находим объект "top" внутри текущего объекта
    const topObject = object.getObjectByName("top", true);

    // Находим объект "row" на сцене
    const rowObject = three.scene.getObjectByName("row", true);

    // Вычисляем ограничивающий бокс для всего объекта
    const objectBox = new THREE.Box3().setFromObject(object);

    // Получаем ширину и глубину основного объекта
    const width = objectBox.max.x - objectBox.min.x;
    const depth = objectBox.max.z - objectBox.min.z;

    // Устанавливаем нижнюю границу бокса
    let bottomY;
    if (rowObject) {
        // Если нашли объект "row", вычисляем его нижнюю границу
        const rowBox = new THREE.Box3().setFromObject(rowObject);
        bottomY = rowBox.min.y;
    } else {
        // Если объект "row" не найден, используем стандартное значение
        bottomY = -0.25;
    }

    // Если объект "top" найден, устанавливаем верхнюю границу ниже него
    let topY;
    if (topObject) {
        const topBox = new THREE.Box3().setFromObject(topObject);
        // Берем позицию нижней части объекта "top" как верхнюю границу нашего бокса
        topY = topBox.min.y;
    } else {
        // Если объект "top" не найден, используем высоту основного объекта
        topY = objectBox.max.y;
    }

    // Проверка на корректность вычисленных границ
    if (topY <= bottomY) {
        // Исправляем, чтобы избежать ошибок с отрицательной или нулевой высотой
        topY = bottomY + 0.1; // минимальная высота 0.1
    }

    // Вычисляем высоту нового бокса
    const height = topY - bottomY;

    // Создаем геометрию с нужными размерами
    const boxGeometry = new THREE.BoxGeometry(width, height, depth);

    // Создаем полупрозрачный материал
    const boxMaterial = new THREE.MeshBasicMaterial({
        color: 0x00ff00,
        transparent: true,
        opacity: 0.3,
        wireframe: true,
    });

    // Создаем меш
    const boxMesh = new THREE.Mesh(boxGeometry, boxMaterial);

    // Устанавливаем позицию центра бокса
    boxMesh.position.x = (objectBox.max.x + objectBox.min.x) / 2;
    boxMesh.position.y = bottomY + height / 2;
    boxMesh.position.z = (objectBox.max.z + objectBox.min.z) / 2;

    // Добавляем на сцену и даем имя для идентификации
    boxMesh.name = HELPER_BOX_SELECTOR;
    three.scene.add(boxMesh);

    console.log(`Создан бокс для вычисления высоты:
      - Ширина: ${width}
      - Высота: ${height} (от ${bottomY} до ${topY})
      - Глубина: ${depth}
      - Позиция: (${boxMesh.position.x}, ${boxMesh.position.y}, ${boxMesh.position.z})`);

    // Получаем мировую позицию
    const worldPosition = new THREE.Vector3();
    boxMesh.getWorldPosition(worldPosition);

    // Возвращаем размеры и меш
    return {
        mesh: boxMesh,
        dimensions: { width, height, depth },
        range: { bottom: bottomY, top: topY },
        position: boxMesh.position.clone(),
        worldPosition: worldPosition,
    };
}

export function updateHeightCalculationBox(
    three,
    object,
    createIfNotExists = true,
) {
    console.group("Обновление бокса для вычисления высоты");
    console.log("Исходный объект:", object);

    // Используем переданное имя бокса
    console.log("Ищем бокс с именем:", HELPER_BOX_SELECTOR);

    // Ищем существующий бокс
    let boxMesh = three.scene.getObjectByName(HELPER_BOX_SELECTOR);
    console.log("Существующий бокс найден:", !!boxMesh);

    // Если бокс не найден и createIfNotExists=true, создаем новый
    if (!boxMesh && createIfNotExists) {
        console.log("Создаем новый бокс, так как существующий не найден");
        const result = addBoxForHeightCalculation(
            three,
            object,
            HELPER_BOX_SELECTOR,
        );
        console.groupEnd();
        return result;
    } else if (!boxMesh) {
        console.warn(
            `Бокс с именем ${HELPER_BOX_SELECTOR} не найден и не будет создан`,
        );
        console.groupEnd();
        return null;
    }

    // Находим объект "top" внутри текущего объекта
    const topObject = object.getObjectByName("top", true);
    console.log('Объект "top" найден:', !!topObject);

    // Находим объект "row" на сцене
    const rowObject = three.scene.getObjectByName("row", true);
    console.log('Объект "row" найден:', !!rowObject);

    // Вычисляем ограничивающий бокс для всего объекта
    const objectBox = new THREE.Box3().setFromObject(object);

    // Получаем ширину и глубину основного объекта
    const width = objectBox.max.x - objectBox.min.x;
    const depth = objectBox.max.z - objectBox.min.z;
    console.log("Рассчитанные ширина и глубина:", { width, depth });

    // Устанавливаем нижнюю границу бокса
    let bottomY;
    if (rowObject) {
        // Если нашли объект "row", вычисляем его нижнюю границу
        const rowBox = new THREE.Box3().setFromObject(rowObject);
        bottomY = rowBox.min.y;
        console.log('Нижняя граница из объекта "row":', bottomY);
    } else {
        // Если объект "row" не найден, используем стандартное значение
        bottomY = -0.25;
        console.log(
            "Нижняя граница установлена на стандартное значение:",
            bottomY,
        );
    }

    // Если объект "top" найден, устанавливаем верхнюю границу ниже него
    let topY;
    if (topObject) {
        const topBox = new THREE.Box3().setFromObject(topObject);
        // Берем позицию нижней части объекта "top" как верхнюю границу нашего бокса
        topY = topBox.min.y;
        console.log('Верхняя граница из нижней части объекта "top":', topY);
    } else {
        // Если объект "top" не найден, используем высоту основного объекта
        topY = objectBox.max.y;
        console.log("Верхняя граница из основного объекта:", topY);
    }

    // Проверка на корректность вычисленных границ
    if (topY <= bottomY) {
        console.warn("ОШИБКА: Верхняя граница меньше или равна нижней!", {
            bottomY,
            topY,
            разница: topY - bottomY,
        });
        // Исправляем, чтобы избежать ошибок с отрицательной или нулевой высотой
        topY = bottomY + 0.1; // минимальная высота 0.1
        console.log("Исправленная верхняя граница:", topY);
    }

    // Вычисляем высоту нового бокса
    const height = topY - bottomY;
    console.log("Рассчитанная высота бокса:", height);

    // Удаляем старую геометрию чтобы избежать утечек памяти
    boxMesh.geometry.dispose();

    // Обновляем геометрию с новыми размерами
    boxMesh.geometry = new THREE.BoxGeometry(width, height, depth);

    // Вычисляем новую позицию бокса
    const newPositionX = (objectBox.max.x + objectBox.min.x) / 2;
    const newPositionY = bottomY + height / 2;
    const newPositionZ = (objectBox.max.z + objectBox.min.z) / 2;

    console.log("Новая рассчитанная позиция:", {
        x: newPositionX,
        y: newPositionY,
        z: newPositionZ,
    });

    // Сохраняем старую позицию для проверки
    const oldPosition = boxMesh.position.clone();

    // Обновляем позицию
    boxMesh.position.set(newPositionX, newPositionY, newPositionZ);

    // Получаем мировую позицию после обновления
    const worldPosition = new THREE.Vector3();
    boxMesh.getWorldPosition(worldPosition);

    // Проверяем разницу между локальной и мировой позицией
    const positionDifference = new THREE.Vector3().subVectors(
        worldPosition,
        boxMesh.position,
    );

    // Если разница большая, пробуем решить проблему
    if (positionDifference.length() > 1.0) {
        console.warn(
            "Большая разница между локальной и мировой позицией:",
            positionDifference.length(),
        );

        // Перемещаем бокс в корень сцены
        if (boxMesh.parent !== three.scene) {
            const parentName = boxMesh.parent
                ? boxMesh.parent.name || "безымянный"
                : "нет";
            console.log(
                `Перемещаем бокс из родителя "${parentName}" в корень сцены`,
            );

            // Запоминаем мировую позицию
            const worldPos = new THREE.Vector3();
            boxMesh.getWorldPosition(worldPos);

            // Удаляем из текущего родителя и добавляем в сцену
            boxMesh.parent.remove(boxMesh);
            three.scene.add(boxMesh);

            // Устанавливаем мировую позицию как локальную для сцены
            boxMesh.position.copy(worldPos);

            // Проверяем новую позицию
            const newWorldPos = new THREE.Vector3();
            boxMesh.getWorldPosition(newWorldPos);
            console.log("Позиция бокса после перемещения:", {
                локальная: boxMesh.position.clone(),
                мировая: newWorldPos,
            });
        }
    }

    console.log("Финальные размеры и позиция бокса:", {
        ширина: width,
        высота: height,
        глубина: depth,
        предыдущая_позиция: oldPosition,
        новая_позиция: boxMesh.position.clone(),
        мировая_позиция: worldPosition,
    });

    console.groupEnd();

    // Возвращаем информацию о боксе
    return {
        mesh: boxMesh,
        dimensions: { width, height, depth },
        range: { bottom: bottomY, top: topY },
        position: boxMesh.position.clone(),
        worldPosition: worldPosition,
        positionChanged: !oldPosition.equals(boxMesh.position),
    };
}

export function setupRotationPointsForHooks(three, object) {
    // Массив пар крюков и клипов
    const hookPairs = [
        {
            hook: "hook_top_right",
            clip: "clip_top_right",
            group: "rotationRightTopGroup",
        },
        {
            hook: "hook_top_left",
            clip: "clip_top_left",
            group: "rotationLeftTopGroup",
        },
        {
            hook: "hook_bottom_right",
            clip: "clip_bottom_right",
            group: "rotationRightBottomGroup",
        },
        {
            hook: "hook_bottom_left",
            clip: "clip_bottom_left",
            group: "rotationLeftBottomGroup",
        },
    ];

    const material = new THREE.MeshPhongMaterial({
        color: 0xffffff,
        specular: 0xffffff,
        shininess: 10,
    });

    hookPairs.forEach((pair) => {
        const hook = object.getObjectByName(pair.hook);
        const clip = object.getObjectByName(pair.clip);

        if (hook && clip) {
            // Создаем группу вращения
            const rotationGroup = new THREE.Mesh(
                new THREE.PlaneGeometry(0, 0),
                material,
            );
            rotationGroup.name = pair.group;

            // Сохраняем мировые позиции объектов
            const hookWorldPosition = new THREE.Vector3();
            const clipWorldPosition = new THREE.Vector3();
            hook.getWorldPosition(hookWorldPosition);
            clip.getWorldPosition(clipWorldPosition);

            // Устанавливаем позицию группы в центр клипа
            const centerWorld = getGeometryCenterWorld(clip);
            const centerLocal = object.worldToLocal(centerWorld.clone());
            rotationGroup.position.copy(centerLocal);

            // Добавляем группу на объект
            object.add(rotationGroup);

            // Пересчитываем локальные позиции относительно группы
            const hookLocal = rotationGroup.worldToLocal(hookWorldPosition);
            const clipLocal = rotationGroup.worldToLocal(clipWorldPosition);

            hook.position.copy(hookLocal);
            clip.position.copy(clipLocal);

            // Добавляем крюк и клип в группу вращения
            rotationGroup.add(clip);
            rotationGroup.add(hook);
        }
    });
}
export function getGeometryCenterWorld(object) {
    const box = new THREE.Box3().setFromObject(object);
    const center = new THREE.Vector3();
    box.getCenter(center);
    return center;
}
export function setupRowModels(three) {
    let models = three.scene.getObjectByName("models");
    let clonedModels = three.scene.getObjectByName("clonedModels");

    console.log(models, clonedModels);

    if (!models || !clonedModels) {
        return;
    }

    let row = models.getObjectByName("row", true);
    let rowFromClone = clonedModels.getObjectByName("row", true);

    if (!row || !rowFromClone) {
        return;
    }

    // Настройка видимости и позиции компонентов
    ["box", "box_medium", "box_large"].forEach((name) => {
        const box = row.getObjectByName(name, true);
        const boxClone = rowFromClone.getObjectByName(name, true);
        if (box) {
            box.position.y = 0.3;
            box.visible = false;
        }
        if (boxClone) {
            boxClone.position.y = 0.3;
            boxClone.visible = false;
        }
    });
    row.visible = false;
    rowFromClone.visible = false;

    three.originalRow = row.clone();
    three.originalClonedRow = rowFromClone.clone();

    console.log(three.originalRow, three.originalClonedRow);

    // Настройка видимости и позиции компонентов
    // ["box", "box_medium", "box_large"].forEach((name) => {
    //     const box = object.getObjectByName(name, true);
    //     if (box) {
    //         box.position.y = 0.3;
    //         box.visible = false;
    //     }
    // });

    // object.visible = false;
    // three.originalRow = object.clone();
}
