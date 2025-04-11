import * as THREE from "three";
import { gsap } from "gsap";
import Toastify from "toastify-js";
import {
    SCALE_FACTOR,
    ROW_HEIGHTS,
    BASE_POSITIONS,
    ROW_CONFIGS,
} from "./constants";

// Конвертация размеров
export function mmToUnits(mm) {
    return mm * SCALE_FACTOR;
}

export function unitsToMm(units) {
    return units / SCALE_FACTOR;
}

// Проверка возможности добавления ряда
export function canAddRow(size, remainingHeight) {
    return remainingHeight >= ROW_HEIGHTS[size];
}

// Расчет позиции для ряда
export function calculateRowPosition(three, rows, rowIndex) {
    const basePosition = three.originalRow
        ? three.originalRow.position.y
        : BASE_POSITIONS[rows[0]?.size || "small"];

    // Для первого ряда используем базовую позицию
    if (rowIndex === 0) return basePosition;

    // Для последующих добавляем высоту предыдущих
    return rows
        .slice(0, rowIndex)
        .reduce(
            (pos, row) => pos + mmToUnits(ROW_HEIGHTS[row.size]),
            basePosition,
        );
}

// Создание ящиков для ряда
export function createBoxesForRow(
    rowClone,
    originalBox,
    config,
    selectedColor,
) {
    const { count, offset } = config;

    Array.from({ length: count }).forEach((_, i) => {
        // Клонирование бокса
        const boxClone = originalBox.clone();
        boxClone.visible = true;

        // Настройка материала
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

        // Позиция и имя
        boxClone.position.set(
            originalBox.position.x + i * offset,
            originalBox.position.y,
            originalBox.position.z,
        );

        const boxName = `box_${i}_${Math.random().toString(36).slice(2, 9)}`;
        boxClone.name = boxName;

        // Добавление и анимация
        rowClone.add(boxClone);
        gsap.to(rowClone.getObjectByName(boxName).position, {
            y: 0,
            duration: 0.15,
            delay: i * 0.05,
            ease: "power3.inOut",
        });
    });
}

// Добавление ящика
export function addBoxToScene(
    three,
    selectedSize,
    selectedColor,
    addedRows,
    rowIndex,
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
    rowClone.visible = true;

    // Получаем нужный тип бокса
    const originalBox = rowClone.getObjectByName(config.selector, true);
    if (!originalBox) {
        console.error(`Бокс ${config.selector} не найден в модели ряда`);
        return;
    }

    // Создаем боксы для ряда
    createBoxesForRow(rowClone, originalBox, config, selectedColor);

    // Определяем индекс и рассчитываем позицию
    const index = rowIndex !== null ? rowIndex : addedRows.length;
    const yPosition = calculateRowPosition(three, addedRows, index);

    // Устанавливаем позицию и имя
    rowClone.position.set(
        three.originalRow.position.x,
        yPosition,
        three.originalRow.position.z,
    );

    rowClone.name = `row_${addedRows.length}`;

    // Добавляем на сцену
    three.scene.add(rowClone);
    three.lastRowPosition = rowClone.position.clone();

    logCallback(`Добавлен ряд #${index} (${selectedSize})`);
    console.log("Scene", three.scene.children);

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
        const message = `Недостаточно места для добавления ящика размера ${selectedSize}. Осталось ${remainingHeight}мм.`;
        logCallback("Нехватка места", { warning: message });

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

    // Проверка порядка размеров
    if (addedRows.length > 0) {
        const lastSize = addedRows[addedRows.length - 1].size;

        if (
            (lastSize === "small" &&
                ["medium", "large"].includes(selectedSize)) ||
            (lastSize === "medium" && selectedSize === "large")
        ) {
            const message = "Выберите ящик меньшего размера.";
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
    const rowToRemove = three.scene.getObjectByName(rowName);
    console.log(rowToRemove);

    if (rowToRemove) {
        three.scene.remove(rowToRemove);

        // Очистка ресурсов
        rowToRemove.traverse((child) => {
            if (child.isMesh) {
                child.geometry?.dispose();
                if (Array.isArray(child.material)) {
                    child.material.forEach((m) => m.dispose());
                } else {
                    child.material?.dispose();
                }
            }
        });
    }

    return true;
}
