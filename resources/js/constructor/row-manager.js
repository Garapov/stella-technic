import * as THREE from "three";
import { gsap } from "gsap";
import Toastify from "toastify-js";
import { SCALE_FACTOR, ROW_CONFIGS, HELPER_BOX_SELECTOR } from "./constants";

// Конвертация размеров
export function mmToUnits(mm) {
    return mm * SCALE_FACTOR;
}

export function unitsToMm(units) {
    return units / SCALE_FACTOR;
}

// Проверка возможности добавления ряда
export function canAddRow(size, remainingHeight) {
    return remainingHeight >= ROW_CONFIGS[size].height;
}

// Расчет позиции для ряда
export function calculateRowPosition(three, rows, rowClone, selectedSize) {
    let basePosition = 0;

    const helper_box = three.scene.getObjectByName(HELPER_BOX_SELECTOR, true);

    const boundingBox = new THREE.Box3().setFromObject(helper_box);
    const rowBoundingBox = new THREE.Box3().setFromObject(rowClone);
    basePosition =
        boundingBox.max.y - (rowBoundingBox.max.y - rowBoundingBox.min.y);

    console.log(
        "basePosition",
        basePosition,
        rowBoundingBox.max.y - rowBoundingBox.min.y,
        boundingBox.max.y,
    );

    rows.forEach((item, index) => {
        basePosition -= ROW_CONFIGS[item.size].height / 1000;
    });
    return basePosition;
}

export function animateBox({ three, rowIndex, boxIndex }) {
    const row = three.scene.getObjectByName(`row_${rowIndex}`, true);
    if (!row) return;
    const box = row.getObjectByName(`box_${boxIndex}`, true);
    if (!box) return;

    let tl = gsap.timeline({ repeat: 0 });
    if (box.position.z > 0) {
        tl.to(box.rotation, {
            x: -0.1,
            duration: 0.2,
            ease: "power3.inOut",
        });
        tl.to(box.position, {
            z: 0,
            duration: 0.2,
            ease: "power3.inOut",
        });
        tl.to(box.rotation, {
            x: 0,
            duration: 0.2,
            ease: "power3.inOut",
        });
    } else {
        tl.to(box.rotation, {
            x: -0.1,
            duration: 0.2,
            ease: "power3.inOut",
        });
        tl.to(box.position, {
            z: 0.15,
            duration: 0.2,
            ease: "power3.inOut",
        });
        tl.to(
            box.rotation,
            {
                x: 0,
                duration: 0.2,
                ease: "power3.inOut",
            },
            0.3,
        );
    }
    tl = null;
    // gsap.to(box.position, {
    //     z: box.position.z > 0 ? 0 : 0.1,
    //     duration: 0.15,
    //     delay: 0.05,
    //     ease: "power3.inOut",
    // });
}

// Создание ящиков для ряда
export function createBoxesForRow(
    rowClone,
    selectedWidth,
    originalBox,
    config,
    selectedColor,
) {
    const count = config[selectedWidth];
    const offset = config["offset"][selectedWidth];

    try {
        // Получаем геометрию оригинального бокса
        const geometry = originalBox.geometry.clone();

        // Создаем новый материал
        const material = new THREE.MeshPhongMaterial({
            color: selectedColor,
            shininess: 100,
        });

        // Создаем InstancedMesh
        const instances = new THREE.InstancedMesh(geometry, material, count);
        instances.name = "boxes_group";

        // Устанавливаем позиции для каждого экземпляра
        const matrix = new THREE.Matrix4();
        for (let i = 0; i < count; i++) {
            const boxName = `box_${i}`;

            // Создаем матрицу трансформации
            matrix.makeTranslation(
                originalBox.position.x + i * offset,
                originalBox.position.y,
                originalBox.position.z,
            );

            // Применяем матрицу к экземпляру
            instances.setMatrixAt(i, matrix);
        }

        // Обновляем матрицы
        instances.instanceMatrix.needsUpdate = true;

        // Добавляем на сцену
        rowClone.add(instances);
    } catch (error) {
        console.error("Error creating InstancedMesh:", error);

        // Альтернативный метод с клонированием
        Array.from({ length: count }).forEach((_, i) => {
            const boxClone = originalBox.clone();
            boxClone.visible = true;

            if (boxClone.material) {
                boxClone.material = Array.isArray(boxClone.material)
                    ? boxClone.material.map((m) => {
                          const clone = m.clone();
                          clone.color.set(selectedColor);
                          return clone;
                      })
                    : (() => {
                          const clone = boxClone.material.clone();
                          clone.color.set(selectedColor);
                          return clone;
                      })();
            }

            boxClone.position.set(
                originalBox.position.x + i * offset,
                originalBox.position.y,
                originalBox.position.z,
            );

            boxClone.name = `box_${i}`;
            boxClone.visible = true;

            rowClone.add(boxClone);
        });
    }
}

// Добавление ящика
export function addBoxToScene(
    three,
    selectedSize,
    selectedWidth,
    selectedHeight,
    selectedColor,
    addedRows,
    rowIndex,
    availableColors,
    logCallback,
) {
    if (!three.originalRow) {
        console.error("Оригинальная модель ряда не найдена");
        return;
    }

    // Получаем параметры для текущего размера
    const config = ROW_CONFIGS[selectedSize];

    // Клонирование ряда
    const rowClone = three.originalRow.clone();
    const rowClonedClone = three.originalClonedRow.clone();

    rowClone.visible = true;
    rowClonedClone.visible = true;

    if (selectedSize == "small") {
        rowClone.remove(rowClone.getObjectByName("box_large"));
        rowClone.remove(rowClone.getObjectByName("box_medium"));
    }
    if (selectedSize == "medium") {
        rowClone.remove(rowClone.getObjectByName("box"));
        rowClone.remove(rowClone.getObjectByName("box_large"));
    }
    if (selectedSize == "large") {
        rowClone.remove(rowClone.getObjectByName("box"));
        rowClone.remove(rowClone.getObjectByName("box_medium"));
    }

    if (selectedSize == "small") {
        rowClonedClone.remove(rowClonedClone.getObjectByName("box_large"));
        rowClonedClone.remove(rowClonedClone.getObjectByName("box_medium"));
    }
    if (selectedSize == "medium") {
        rowClonedClone.remove(rowClonedClone.getObjectByName("box"));
        rowClonedClone.remove(rowClonedClone.getObjectByName("box_large"));
    }
    if (selectedSize == "large") {
        rowClonedClone.remove(rowClonedClone.getObjectByName("box"));
        rowClonedClone.remove(rowClonedClone.getObjectByName("box_medium"));
    }

    // Получаем нужный тип бокса
    const originalBox = rowClone.getObjectByName(config.selector, true);
    const originalClonedBox = rowClonedClone.getObjectByName(
        config.selector,
        true,
    );
    if (!originalBox || !originalClonedBox) {
        console.error(`Бокс ${config.selector} не найден в модели ряда`);
        return;
    }

    // Создаем боксы для ряда
    createBoxesForRow(
        rowClone,
        selectedWidth,
        originalBox,
        config,
        selectedColor,
    );
    createBoxesForRow(
        rowClonedClone,
        selectedWidth,
        originalClonedBox,
        config,
        selectedColor,
    );

    const yPosition = calculateRowPosition(
        three,
        addedRows,
        rowClone,
        selectedSize,
    );

    // Устанавливаем позицию и имя
    rowClone.position.set(
        selectedWidth == "wide"
            ? three.originalRow.position.x + 0.215
            : three.originalRow.position.x,
        yPosition,
        three.originalRow.position.z,
    );
    rowClonedClone.position.set(
        selectedWidth == "wide"
            ? three.originalRow.position.x + 0.215
            : three.originalRow.position.x,
        yPosition,
        three.originalRow.position.z,
    );

    rowClone.name = `row_${addedRows.length}`;
    rowClonedClone.name = `row_${addedRows.length}`;

    rowClone.getObjectByName("lineClone").visible = selectedWidth == "wide";
    rowClonedClone.getObjectByName("lineClone").visible =
        selectedWidth == "wide";

    // Добавляем на сцену
    three.scene.getObjectByName("models").add(rowClone);
    three.scene.getObjectByName("clonedModels").add(rowClonedClone);
    three.lastRowPosition = rowClone.position.clone();

    return rowClone;
}

// Проверка на возможность добавления ряда
export function validateRowAddition(
    selectedSize,
    addedRows,
    remainingHeight,
    logCallback,
) {
    // Проверка на доступное пространство
    if (!canAddRow(selectedSize, remainingHeight)) {
        const message = `Недостаточно места для добавления ящика.`;
        logCallback("Нехватка места", { warning: message });

        // if (!("Notification" in window)) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "bottom",
            position: "right",
            style: { background: "red" },
        }).showToast();
        // } else {
        //     Notification.requestPermission((permission) => {
        //         let notification = new Notification("Добавление ряда ящиков", {
        //             body: message, // content for the alert
        //             icon: "https://pusher.com/static_logos/320x320.png", // optional image url
        //         });
        //     });
        // }

        return false;
    }

    // Проверка порядка размеров
    if (addedRows.length > 0) {
        const lastSize = addedRows[addedRows.length - 1].size;

        if (
            (lastSize === "large" &&
                ["medium", "small"].includes(selectedSize)) ||
            (lastSize === "medium" && selectedSize === "small")
        ) {
            const message = "Выберите ящик большего размера.";
            logCallback("Неверный размер", { warning: message });

            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "bottom",
                position: "right",
                style: { background: "red" },
            }).showToast();

            return false;
        }
    }

    return true;
}

// Удаление ряда
export function removeRowFromScene(three, index, addedRows, logCallback) {
    if (index < 0 || index >= addedRows.length) {
        logCallback(`Попытка удаления несуществующего ряда #${index}`, {
            warning: "Индекс вне диапазона",
        });
        return false;
    }

    logCallback(`Удаление ряда #${index}`);

    // Находим объект ряда
    const rowName = `row_${index}`;
    const models = three.scene.getObjectByName("models");
    const clonedModels = three.scene.getObjectByName("clonedModels");
    const rowToRemove = models.getObjectByName(rowName);
    const cloneRowToRemove = clonedModels.getObjectByName(rowName);

    if (rowToRemove && cloneRowToRemove) {
        models.remove(rowToRemove);
        clonedModels.remove(cloneRowToRemove);
    }

    return true;
}
