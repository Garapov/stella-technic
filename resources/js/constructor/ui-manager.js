import ThreeMeshUI from "three-mesh-ui";
import * as THREE from "three";

// Переменная для отслеживания текущего выбранного объекта
let selectedObject = null;

const TRASH_ICON =
    '<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none"><path d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

// Функция для создания UI для строки
export async function createRowUI(three, row, colors, deleteFunction, changeRowColorFunction) {
    // Создаем текстуру для кнопки удаления
    const deleteTexture = await createSVGTexture(TRASH_ICON, 64, 64);

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
    container.name = `settings_${row.name}`;

    

    

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
                console.log(color);
                changeRowColorFunction(row, color);
            }
        });

        // ВАЖНО: помечаем как UI элемент для распознавания
        button.isUI = true;
        button.isInteractive = true;

        // Добавляем в список объектов для тестирования
        three.objectsToTest.push(button);

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
        backgroundTexture: deleteTexture,
        backgroundOpacity: 1,
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
            console.log(row.name);
            deleteFunction();
            three.objectsToTest = [];
            // three.scene.delete(row);
        },
    });

    // ВАЖНО: помечаем как UI элемент для распознавания
    deleteButton.isUI = true;
    deleteButton.isInteractive = true;

    // Добавляем в список объектов для тестирования
    three.objectsToTest.push(deleteButton);

    // Добавляем кнопку удаления в контейнер
    container.add(deleteButton);

    // Обновляем ThreeMeshUI после создания всех элементов
    ThreeMeshUI.update();

    return container;
}

function createSVGTexture(svgContent, width, height) {
    // Создаем временный DOM-элемент для SVG
    const svgBlob = new Blob([svgContent], { type: "image/svg+xml" });
    const url = URL.createObjectURL(svgBlob);

    // Создаем скрытый canvas для рендеринга SVG
    const canvas = document.createElement("canvas");
    canvas.width = width || 64;
    canvas.height = height || 64;
    const ctx = canvas.getContext("2d");

    // Создаем изображение и загружаем SVG
    const img = new Image();
    const texture = new THREE.CanvasTexture(canvas);

    // Возвращаем промис, который разрешится, когда текстура будет готова
    return new Promise((resolve) => {
        img.onload = () => {
            // Рисуем SVG на canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "rgba(255, 0, 0, 0.0)"; // Прозрачный фон
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            // Обновляем текстуру
            texture.needsUpdate = true;
            URL.revokeObjectURL(url);

            resolve(texture);
        };

        img.src = url;
    });
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

        console.log('intersects', selectedObject);
    } else {
        // Если нет пересечений, рисуем луч по направлению
        const direction = raycaster.ray.direction.clone().normalize();
        endPoint = startPoint.clone().add(direction.multiplyScalar(100));
        console.log('not intersects', selectedObject);
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
