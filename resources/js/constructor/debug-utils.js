// Форматирование позиции для вывода
const fpsData = {
    frames: 0,
    lastTime: performance.now(),
    fpsValue: "N/A",
    updateInterval: 1000, // обновлять значение FPS каждую секунду
};

export function formatPosition(position) {
    return Object.fromEntries(
        ["x", "y", "z"].map((axis) => [axis, position[axis].toFixed(3)]),
    );
}

export function updateFPS() {
    fpsData.frames++;

    const currentTime = performance.now();
    const elapsedTime = currentTime - fpsData.lastTime;

    if (elapsedTime >= fpsData.updateInterval) {
        fpsData.fpsValue = Math.round((fpsData.frames * 1000) / elapsedTime);
        fpsData.frames = 0;
        fpsData.lastTime = currentTime;
    }

    return fpsData.fpsValue;
}

// Обновление отладочной информации
export function updateDebugInfo(three, debugInfo, debugMode, addedRows) {
    if (!debugMode) return debugInfo;

    const updatedInfo = { ...debugInfo };

    // Добавляем значение FPS
    updatedInfo.fps = fpsData.fpsValue;

    // Информация о камере (проверяем наличие камеры)
    if (three.camera) {
        updatedInfo.cameraPosition = {
            x: three.camera.position.x.toFixed(3),
            y: three.camera.position.y.toFixed(3),
            z: three.camera.position.z.toFixed(3),
        };
    }

    // Информация о сцене
    if (three.scene) {
        let objectCount = 0;
        three.scene.traverse(() => objectCount++);
        updatedInfo.modelCount = objectCount;

        // Информация об объектах
        updatedInfo.sceneObjects = [];
        updatedInfo.allRowsOnScene = [];

        three.scene.children.forEach((child) => {
            if (child.name?.startsWith("row_")) {
                updatedInfo.sceneObjects.push({
                    name: child.name,
                    type: child.type,
                    visible: child.visible,
                    position: formatPosition(child.position),
                    isRow: true,
                });

                updatedInfo.allRowsOnScene.push({
                    name: child.name,
                    position: formatPosition(child.position),
                });
            } else {
                updatedInfo.sceneObjects.push({
                    name: child.name || "unnamed",
                    type: child.type,
                    visible: child.visible,
                    position: formatPosition(child.position),
                });
            }
        });
    }

    // Информация о рядах
    updatedInfo.rowsPositions = addedRows.map((row, index) => {
        const rowName = `row_${index}`;
        const rowObj = three.scene.getObjectByName(rowName);

        return {
            index,
            size: row.size,
            color: row.color,
            name: rowName,
            found: Boolean(rowObj),
            position: rowObj ? formatPosition(rowObj.position) : "not found",
        };
    });

    // Информация о памяти
    if (window.performance?.memory) {
        const memory = window.performance.memory;
        updatedInfo.memoryUsage = `${Math.round(memory.usedJSHeapSize / 1048576)} MB / ${Math.round(memory.jsHeapSizeLimit / 1048576)} MB`;
    }

    updatedInfo.renderFrames++;

    return updatedInfo;
}

// Логирование с учетом режима отладки
export function log(message, data, debugMode, debugInfo) {
    if (!debugMode) return debugInfo;

    const timestamp = new Date().toLocaleTimeString();
    const updatedInfo = {
        ...debugInfo,
        lastAction: `${timestamp}: ${message}`,
    };

    if (data?.warning) {
        updatedInfo.warnings = [
            ...debugInfo.warnings,
            {
                time: timestamp,
                message: data.warning,
            },
        ];

        if (updatedInfo.warnings.length > 10) updatedInfo.warnings.shift();
    }

    console.log(`[DEBUG] ${message}`, data);

    return updatedInfo;
}
