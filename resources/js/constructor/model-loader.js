import * as THREE from "three";
import { OBJLoader } from "three/addons/loaders/OBJLoader.js";
import { MTLLoader } from "three/addons/loaders/MTLLoader.js";
import { rotate } from "three/src/nodes/TSL.js";

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
    if (object.name == "shelf") setupRotationPointsForHooks(three, object);
    model.object = object;

    // Сообщаем о 100% прогрессе
    if (progressCallback) progressCallback(100);

    return object;
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
