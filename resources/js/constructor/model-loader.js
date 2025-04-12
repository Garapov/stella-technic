import * as THREE from "three";
import { OBJLoader } from "three/addons/loaders/OBJLoader.js";
import { MTLLoader } from "three/addons/loaders/MTLLoader.js";

/**
 * Загружает все 3D модели
 * @param {Object} three Объект Three.js контейнера
 * @param {Array} models Массив моделей для загрузки
 * @param {Function} logCallback Функция для логирования
 * @param {Function} progressCallback Функция для обновления прогресса
 * @returns {Promise<boolean>} Статус загрузки
 */
export async function loadModels(three, models, logCallback, progressCallback) {
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
            );
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
    const materials = await new Promise((resolve, reject) => {
        new MTLLoader().load(
            model.mtl,
            (materials) => resolve(materials),
            // Прогресс загрузки материалов
            (xhr) => {
                if (xhr.lengthComputable) {
                    const progress = (xhr.loaded / xhr.total) * 50; // Материалы - 50% общего прогресса
                    if (progressCallback) progressCallback(progress);
                }
            },
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
        setupRowModel(three, object);
    }

    // Добавление на сцену
    three.scene.add(object);
    model.object = object;

    // Сообщаем о 100% прогрессе
    if (progressCallback) progressCallback(100);

    return object;
}

/**
 * Настраивает модель ряда
 * @param {Object} three Объект Three.js контейнера
 * @param {Object} object Объект модели ряда
 */
export function setupRowModel(three, object) {
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
}
