import ThreeMeshUI from "three-mesh-ui";
import * as THREE from "three";

// Переменная для отслеживания текущего выбранного объекта
let selectedObject = null;

const TRASH_ICON =
    '<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none"><path d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
const SETTINGS_ICON =
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="#ffffff" d="M27.92,18.46l-1.14-.92a.86.86,0,0,1-.31-.65V15.2a.83.83,0,0,1,.31-.65l1.11-.89A2.86,2.86,0,0,0,28.54,10L27.42,8.12A2.76,2.76,0,0,0,24.21,6.9l-1.82.58a.73.73,0,0,1-.64-.09l-1.23-.81A.8.8,0,0,1,20.16,6L19.92,4.4A2.77,2.77,0,0,0,17.18,2H14.93a2.79,2.79,0,0,0-2.77,2.51L12,6a.8.8,0,0,1-.41.63L10,7.48a.79.79,0,0,1-.67,0L8.08,7A2.75,2.75,0,0,0,4.62,8.1L3.48,10a2.84,2.84,0,0,0,.57,3.64L5.4,14.75a.87.87,0,0,1,.29.62l0,1.27a.87.87,0,0,1-.28.65L4,18.61a2.85,2.85,0,0,0-.57,3.52l1,1.84a2.77,2.77,0,0,0,3.42,1.22l1.62-.63a.73.73,0,0,1,.66.05l1.4.84a.76.76,0,0,1,.38.55l.32,1.66A2.81,2.81,0,0,0,15,29.94h2.15a2.79,2.79,0,0,0,2.75-2.39l.26-1.65a.85.85,0,0,1,.4-.6l1.3-.74a.78.78,0,0,1,.65,0l1.64.63a2.76,2.76,0,0,0,3.37-1.2l1.08-1.83A2.86,2.86,0,0,0,27.92,18.46ZM26.85,21.1l-1.08,1.83a.78.78,0,0,1-.94.34l-1.64-.63a2.77,2.77,0,0,0-2.35.19l-1.3.74a2.81,2.81,0,0,0-1.39,2l-.26,1.66a.78.78,0,0,1-.77.69H15a.79.79,0,0,1-.77-.66l-.32-1.66a2.81,2.81,0,0,0-1.33-1.89l-1.4-.84a2.73,2.73,0,0,0-1.41-.39,2.91,2.91,0,0,0-1,.19l-1.62.64a.77.77,0,0,1-1-.35l-1-1.84a.84.84,0,0,1,.16-1l1.48-1.32a2.87,2.87,0,0,0,.94-2.17l0-1.27a2.9,2.9,0,0,0-1-2.11L5.36,12.08A.85.85,0,0,1,5.19,11L6.33,9.14a.76.76,0,0,1,1-.32l1.25.53A2.72,2.72,0,0,0,11,9.23l1.61-.89A2.81,2.81,0,0,0,14,6.17l.16-1.46A.79.79,0,0,1,14.93,4h2.24a.79.79,0,0,1,.78.7l.23,1.61a2.85,2.85,0,0,0,1.24,2l1.23.8A2.72,2.72,0,0,0,23,9.38l1.82-.57a.76.76,0,0,1,.89.34L26.83,11a.86.86,0,0,1-.19,1.09L25.53,13a2.84,2.84,0,0,0-1.06,2.22v1.69a2.81,2.81,0,0,0,1.05,2.2l1.14.92A.86.86,0,0,1,26.85,21.1Z"/><path fill="#ffffff" d="M16,11a5,5,0,1,0,5,5A5,5,0,0,0,16,11Zm0,8a3,3,0,1,1,3-3A3,3,0,0,1,16,19.07Z"/></svg>';

// Функция для создания UI для строки
export async function createRowUI(
    three,
    row,
    colors,
    deleteFunction,
    changeRowColorFunction,
) {
    // Убедимся, что имя ряда существует
    const rowName =
        row.name || `row_${Math.random().toString(36).substr(2, 9)}`;

    // Создаем текстуры для кнопок
    const deleteTexture = await createSVGTexture(
        TRASH_ICON,
        64,
        64,
        "rgba(255, 0, 0, 0)",
    );
    const settingsTexture = await createSVGTexture(
        SETTINGS_ICON,
        64,
        64,
        "rgba(255, 255, 255, 0)",
    );

    // Создание основного контейнера (без фона, только для группировки)
    const container = new ThreeMeshUI.Block({
        wrapContent: true,
        contentDirection: "row",
        justifyContent: "start",
        alignItems: "center",
        backgroundOpacity: 0, // Прозрачный фон
        fontFamily: "/assets/models/Roboto-msdf.json",
        fontTexture: "/assets/models/Roboto-msdf.png",
    });
    container.name = `ui_container_${rowName}`;

    // Создаем блок для кнопки настроек (с фоном)
    const settingsButtonBlock = new ThreeMeshUI.Block({
        wrapContent: true,
        padding: 0.01,
        backgroundColor: new THREE.Color(0x222222),
        backgroundOpacity: 0.8,
        borderRadius: 0.01,
    });
    settingsButtonBlock.name = `settings_button_block_${rowName}`;

    // Создаем панель настроек (с фоном), которая будет скрыта изначально
    const settingsPanel = new ThreeMeshUI.Block({
        wrapContent: true,
        margin: 0.01, // Отступ между блоками
        padding: 0.01,
        contentDirection: "row",
        justifyContent: "center",
        alignItems: "center",
        fontFamily: "/assets/models/Roboto-msdf.json",
        fontTexture: "/assets/models/Roboto-msdf.png",
        backgroundColor: new THREE.Color(0x222222),
        backgroundOpacity: 0.8,
        borderRadius: 0.01,
        visible: false, // Изначально скрыта
    });
    settingsPanel.name = `settings_panel_${rowName}`;

    // Убедимся, что массив для интерактивных объектов существует
    if (!three.objectsToTest) {
        three.objectsToTest = [];
    }

    // Создаем кнопки цветов и добавляем их в панель настроек
    colors.forEach((color, index) => {
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
        button.name = `color_button_${rowName}_${index}_${color.replace("#", "")}`;

        // Настройка состояний кнопки
        button.setupState({
            state: "idle",
            attributes: {
                backgroundColor: new THREE.Color(color),
                backgroundOpacity: 1,
            },
        });

        button.setupState({
            state: "hovered",
            attributes: {
                backgroundColor: new THREE.Color(color),
                backgroundOpacity: 0.7,
            },
        });

        button.setupState({
            state: "selected",
            attributes: {
                backgroundColor: new THREE.Color(color),
                backgroundOpacity: 1,
            },
            onSet: () => {
                changeRowColorFunction(row, color);

                // После изменения цвета сворачиваем панель
                toggleSettingsPanel(false);
            },
        });

        // Помечаем как UI элемент
        button.isUI = true;
        button.isInteractive = true;

        // Добавляем в список объектов для тестирования
        three.objectsToTest.push(button);

        // Добавляем кнопку в панель настроек
        settingsPanel.add(button);
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
    deleteButton.name = `delete_button_${rowName}`;

    // Настройка состояний кнопки удаления
    deleteButton.setupState({
        state: "idle",
        attributes: {
            backgroundColor: new THREE.Color(0xff0000),
            backgroundOpacity: 1,
        },
    });

    deleteButton.setupState({
        state: "hovered",
        attributes: {
            backgroundColor: new THREE.Color(0xff5555),
            backgroundOpacity: 1,
        },
    });

    deleteButton.setupState({
        state: "selected",
        attributes: {
            backgroundColor: new THREE.Color(0xff0000),
            backgroundOpacity: 1,
        },
        onSet: function () {
            // Находим все элементы UI, связанные с этим рядом
            const rowUIElements = three.objectsToTest.filter(
                (obj) => obj.name && obj.name.includes(rowName),
            );

            // Вызываем функцию удаления
            deleteFunction();

            // Сбрасываем выбранный объект, если он связан с удаляемым рядом
            if (
                selectedObject &&
                selectedObject.name &&
                selectedObject.name.includes(rowName)
            ) {
                selectedObject = null;
            }
        },
    });

    // Помечаем как UI элемент
    deleteButton.isUI = true;
    deleteButton.isInteractive = true;

    // Добавляем в список объектов для тестирования
    three.objectsToTest.push(deleteButton);

    // Добавляем кнопку удаления в панель настроек
    settingsPanel.add(deleteButton);

    // Создание кнопки настроек (она будет всегда видна)
    const settingsButton = new ThreeMeshUI.Block({
        height: 0.05,
        width: 0.05,
        margin: 0.005,
        justifyContent: "center",
        alignItems: "center",
        backgroundTexture: settingsTexture,
        backgroundOpacity: 1,
        backgroundColor: new THREE.Color(0xffffff),
        borderRadius: 0.01,
    });
    settingsButton.name = `settings_button_${rowName}`;

    // Настройка состояний кнопки настроек
    settingsButton.setupState({
        state: "idle",
        attributes: {
            backgroundColor: new THREE.Color(0xffffff),
            backgroundOpacity: 1,
        },
    });

    settingsButton.setupState({
        state: "hovered",
        attributes: {
            backgroundColor: new THREE.Color(0xcccccc),
            backgroundOpacity: 1,
        },
    });

    settingsButton.setupState({
        state: "selected",
        attributes: {
            backgroundColor: new THREE.Color(0xffffff),
            backgroundOpacity: 1,
        },
        onSet: function () {
            // Переключаем видимость панели настроек
            const newState = !settingsPanel.visible;
            toggleSettingsPanel(newState);
        },
    });

    // Помечаем как UI элемент
    settingsButton.isUI = true;
    settingsButton.isInteractive = true;

    // Добавляем в список объектов для тестирования
    three.objectsToTest.push(settingsButton);

    // Добавляем кнопку настроек в ее блок
    settingsButtonBlock.add(settingsButton);
    settingsPanel.visible = false;
    // Добавляем блок кнопки настроек и панель настроек в основной контейнер
    container.add(settingsPanel);
    container.add(settingsButtonBlock);

    // Функция для переключения видимости панели настроек
    function toggleSettingsPanel(show) {
        three.scene.getObjectByName(settingsPanel.name).visible =
            !three.scene.getObjectByName(settingsPanel.name).visible;

        // Обновляем интерфейс
        ThreeMeshUI.update();
    }

    // Обновляем ThreeMeshUI после создания всех элементов
    ThreeMeshUI.update();

    return container;
}

function createSVGTexture(
    svgContent,
    width,
    height,
    color = "rgba(255, 0, 0, 0.0)",
) {
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
            ctx.fillStyle = color; // Прозрачный фон
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

    // Обновляем ThreeMeshUI
    ThreeMeshUI.update();

    // Обработка основного луча
    const mainHit = updateSingleRaycaster(
        three.raycaster,
        three.mouse,
        three.camera,
        three.rayLine,
        three.intersectionSphere,
        three,
        0xff0000, // Красный для обычного луча
        0xff6600, // Оранжевый для луча при пересечении
    );
    let currentHit = null;

    if (three.cameraRTTProjection) {

    // Обработка проекционного луча
        const projHit = updateSingleRaycaster(
            three.raycasterProjection,
            three.mouseProjection,
            three.cameraRTTProjection,
            three.rayLineProj,
            three.intersectionSphereProj,
            three,
            0x00ffff, // Голубой для обычного луча
            0x00ff00, // Зеленый для луча при пересечении
        );
    

        // Получаем текущий хит - или от основного, или от проекционного луча
        currentHit = mainHit || projHit;
    } else {
        currentHit = mainHit;
    }

    // Проверяем, существует ли выбранный объект в списке объектов для тестирования
    if (selectedObject && three.objectsToTest.indexOf(selectedObject) === -1) {
        // Если объект был удален, сбрасываем выбранный объект
        selectedObject = null;
    }

    // Если есть новый объект под курсором, и это не текущий выбранный объект
    if (currentHit && currentHit !== selectedObject) {
        // Если был выбран другой объект, сбрасываем его состояние
        if (selectedObject) {
            try {
                selectedObject.setState("idle");
            } catch (e) {
                console.error("Error setting idle state:", e);
                // Если возникла ошибка, возможно, объект был удален - сбрасываем выбор
                selectedObject = null;
            }
        }

        // Устанавливаем новый выбранный объект
        selectedObject = currentHit;

        // И устанавливаем ему состояние "hovered"
        try {
            selectedObject.setState("hovered");
        } catch (e) {
            console.error("Error setting hovered state:", e);
            selectedObject = null;
        }
    }
    // Если нет нового объекта, но есть выбранный - сбрасываем его
    else if (!currentHit && selectedObject) {
        try {
            selectedObject.setState("idle");
        } catch (e) {
            console.error("Error setting idle state:", e);
        }
        selectedObject = null;
    }

    // Обработка клика
    if (selectedObject && (three.mouseClick || three.mouseClickProjection)) {
        // Проверяем, что объект все еще существует и имеет метод setState
        if (typeof selectedObject.setState === "function") {
            try {
                selectedObject.setState("selected");
                selectedObject = null;
            } catch (e) {
                console.error("Error setting selected state:", e);
                selectedObject = null;
            }
        } else {
            console.warn("Selected object no longer has setState method");
            selectedObject = null;
        }

        // Сбрасываем флаги клика
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
    if (!rayLine) return null;

    // Начальная точка луча - позиция камеры
    const startPoint = camera.position.clone();

    // Проверяем, что координаты мыши валидны
    if (Math.abs(mouseCoords.x) > 1 || Math.abs(mouseCoords.y) > 1) {
        return null;
    }

    // Обновляем рейкастер
    raycaster.setFromCamera(mouseCoords, camera);

    // Проверяем пересечения со всеми UI объектами
    const uiObjects = three.objectsToTest.filter((obj) => obj && obj.isUI);

    // Проверяем пересечения, включая дочерние объекты
    const intersects = raycaster.intersectObjects(uiObjects, true);

    // Переменные для отрисовки луча
    let endPoint;
    let currentColor = defaultColor;
    let hitObject = null;

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

        // Перебираем все пересечения и ищем UI-объект
        for (let i = 0; i < intersects.length; i++) {
            let obj = intersects[i].object;
            if (!obj) continue;

            // Находим родительский UI-компонент
            while (obj && !obj.isUI && obj.parent) {
                obj = obj.parent;
            }

            // Если нашли UI-компонент с возможностью смены состояния и он видимый
            if (
                obj &&
                obj.isUI &&
                typeof obj.setState === "function" &&
                obj.visible
            ) {
                hitObject = obj;
                break;
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
    if (rayLine.geometry) rayLine.geometry.dispose();
    rayLine.geometry = new THREE.BufferGeometry().setFromPoints([
        startPoint,
        endPoint,
    ]);
    rayLine.material.color.set(currentColor);

    // Возвращаем найденный объект
    return hitObject;
}
