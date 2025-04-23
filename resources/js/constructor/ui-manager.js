import ThreeMeshUI from "three-mesh-ui";
import * as THREE from "three";

// Переменная для отслеживания текущего выбранного объекта
let selectedObject = null;

// Функция для создания UI для строки
export function createRowUI(three, row, colors) {
    // Создание основного контейнера
    const container = new ThreeMeshUI.Block({
        wrapContent: true,
        padding: 0.01,
        contentDirection: "row",
        justifyContent: "center",
        alignItems: "center",
        fontFamily: "/assets/models/Roboto-msdf.json",
        fontTexture: "/assets/models/Roboto-msdf.png",
        backgroundColor: new THREE.Color(0x222222),
        backgroundOpacity: 0.8,
        borderRadius: 0.01,
    });

    // Устанавливаем позицию контейнера
    container.position.set(
        row.position.clone().x + 0.5,
        row.position.clone().y + 0.09,
        row.position.clone().z + 0.35,
    );

    container.parent = row;

    // Добавляем в сцену
    three.scene.add(container);

    // Убедимся, что массив для интерактивных объектов существует
    if (!three.objectsToTest) {
        three.objectsToTest = [];
    }

    // Создаем кнопки цветов
    colors.forEach((color) => {
        // Создаем кнопку
        const button = new ThreeMeshUI.Block({
            height: 0.03,
            width: 0.03,
            margin: 0.005,
            justifyContent: "center",
            alignItems: "center",
            backgroundColor: new THREE.Color(color),
            borderRadius: 0.01,
        });

        // Настройка состояний кнопки (как в официальном примере)
        button.setupState({
            state: "idle",
            attributes: {
                backgroundColor: new THREE.Color(color),
                backgroundOpacity: 0.8,
                offset: 0.01,
            },
        });

        button.setupState({
            state: "hovered",
            attributes: {
                backgroundColor: new THREE.Color(color),
                backgroundOpacity: 1,
                offset: 0.02,
            },
            onSet: () => {
                console.log(`Button ${color} hovered!`);
            },
        });

        button.setupState({
            state: "selected",
            attributes: {
                backgroundColor: new THREE.Color(color).multiplyScalar(1.2),
                backgroundOpacity: 1,
                offset: 0.01,
            },
            onSet: () => {
                row.traverse(function (child) {
                    if (child.name.includes("box")) {
                        child.material.color = new THREE.Color(color);
                    }
                });
            },
        });

        // ВАЖНО: помечаем как UI элемент для распознавания
        button.isUI = true;
        button.isInteractive = true;

        // Добавляем в список объектов для тестирования
        three.objectsToTest.push(button);
        console.log(`Added color button ${color} to objectsToTest`);

        // Добавляем кнопку в контейнер
        container.add(button);
    });

    // Создание кнопки удаления
    const deleteButton = new ThreeMeshUI.Block({
        height: 0.05,
        width: 0.05,
        margin: 0.005,
        justifyContent: "center",
        alignItems: "center",
        backgroundColor: new THREE.Color(0xff0000),
        borderRadius: 0.01,
    });

    // Настройка состояний кнопки удаления
    deleteButton.setupState({
        state: "idle",
        attributes: {
            backgroundColor: new THREE.Color(0xff0000),
            backgroundOpacity: 0.8,
            offset: 0.01,
        },
    });

    deleteButton.setupState({
        state: "hovered",
        attributes: {
            backgroundColor: new THREE.Color(0xff0000),
            backgroundOpacity: 1,
            offset: 0.02,
        },
        onSet: () => {
            console.log(`Delete button hovered!`);
        },
    });

    deleteButton.setupState({
        state: "selected",
        attributes: {
            backgroundColor: new THREE.Color(0x990000),
            backgroundOpacity: 1,
            offset: 0.01,
        },
        onSet: function () {
            three.scene.delete(row);
        },
    });

    // ВАЖНО: помечаем как UI элемент для распознавания
    deleteButton.isUI = true;
    deleteButton.isInteractive = true;

    // Добавляем в список объектов для тестирования
    three.objectsToTest.push(deleteButton);
    console.log("Added delete button to objectsToTest");

    // Добавляем кнопку удаления в контейнер
    container.add(deleteButton);

    // Обновляем ThreeMeshUI после создания всех элементов
    ThreeMeshUI.update();

    return container;
}

// Функция для обновления рейкастинга и обработки интерактивных элементов
export function updateRaycasting(three) {
    // Если нет объектов для проверки, выходим
    if (!three.objectsToTest || !three.objectsToTest.length) {
        return;
    }

    ThreeMeshUI.update();

    // Обработка основного луча
    updateSingleRaycaster(
        three.raycaster,
        three.mouse,
        three.camera,
        three.rayLine,
        three.intersectionSphere,
        three,
        0xff0000, // Красный для обычного луча
        0xff6600, // Оранжевый для луча при пересечении
    );

    // Обработка проекционного луча
    updateSingleRaycaster(
        three.raycasterProjection,
        three.mouseProjection,
        three.cameraRTTProjection,
        three.rayLineProj,
        three.intersectionSphereProj,
        three,
        0x00ffff, // Голубой для обычного луча
        0x00ff00, // Зеленый для луча при пересечении
    );

    // Обработка кликов, если есть выбранный объект
    if (selectedObject && (three.mouseClick || three.mouseClickProjection)) {
        selectedObject.setState("selected");
        three.mouseClick = false;
        three.mouseClickProjection = false;
    }
}

// Функция для обновления отдельного рейкастера
function updateSingleRaycaster(
    raycaster,
    mouseCoords,
    camera,
    rayLine,
    sphere,
    three,
    defaultColor,
    hitColor,
) {
    if (!rayLine) return;

    // Начальная точка луча - позиция камеры
    const startPoint = camera.position.clone();

    // Проверяем, что координаты мыши валидны
    if (Math.abs(mouseCoords.x) > 1 || Math.abs(mouseCoords.y) > 1) {
        return;
    }

    // Обновляем рейкастер
    raycaster.setFromCamera(mouseCoords, camera);

    // Проверяем пересечения со всеми объектами и их потомками
    const allObjects = [];

    // Собираем все объекты и их потомков
    three.objectsToTest.forEach((obj) => {
        allObjects.push(obj);
        obj.traverse((child) => {
            if (child !== obj) {
                allObjects.push(child);
            }
        });
    });

    // Проверяем пересечения
    const intersects = raycaster.intersectObjects(allObjects, false);

    // Переменные для отрисовки луча
    let endPoint;
    let currentColor = defaultColor;

    if (intersects.length > 0) {
        // Получаем точку пересечения
        endPoint = intersects[0].point;
        currentColor = hitColor;

        // Показываем сферу в точке пересечения
        if (sphere) {
            sphere.position.copy(endPoint);
            sphere.visible = rayLine.visible;
            sphere.material.color.set(hitColor);
        }

        // Ищем объект UI среди пересечений
        let objectHit = intersects[0].object;
        let uiParent = null;

        // Находим ближайший родительский UI объект
        while (objectHit) {
            if (objectHit.isUI || (objectHit.parent && objectHit.parent.isUI)) {
                // Проверяем есть ли метод setState у объекта
                if (objectHit.setState) {
                    uiParent = objectHit;
                    break;
                }
                // Проверяем метод setState у родителя
                else if (objectHit.parent && objectHit.parent.setState) {
                    uiParent = objectHit.parent;
                    break;
                }
            }

            objectHit = objectHit.parent;
        }

        if (uiParent) {
            // Если ранее был выбран другой объект, сбрасываем его состояние
            if (selectedObject && selectedObject !== uiParent) {
                try {
                    selectedObject.setState("idle");
                } catch (e) {
                    console.error("Error setting idle state:", e);
                }
            }

            // Устанавливаем новый выбранный объект
            selectedObject = uiParent;

            // Устанавливаем состояние "hovered"
            try {
                selectedObject.setState("hovered");
            } catch (e) {
                console.error("Error setting hovered state:", e);
            }
        }
    } else {
        // Если нет пересечений, рисуем луч по направлению
        const direction = raycaster.ray.direction.clone().normalize();
        endPoint = startPoint.clone().add(direction.multiplyScalar(100));

        // Скрываем сферу
        if (sphere) {
            sphere.visible = false;
        }
    }

    // Обновляем геометрию луча
    rayLine.geometry.dispose();
    rayLine.geometry = new THREE.BufferGeometry().setFromPoints([
        startPoint,
        endPoint,
    ]);
    rayLine.material.color.set(currentColor);
}
