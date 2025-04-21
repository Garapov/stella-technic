// Константы размеров
export const SHELF_HEIGHT = 1450; // мм
export const ROW_HEIGHTS = { small: 107.68, medium: 155.55, large: 175 };
export const SCALE_FACTOR = 0.001;
export const BASE_POSITIONS = { small: 0.176, medium: 0.213, large: 0.23 };
export const ROW_CONFIGS = {
    small: {
        selector: "box",
        slim: 6,
        wide: 10,
        offset: { slim: -0.106, wide: -0.106 },
    },
    medium: {
        selector: "box_medium",
        slim: 4,
        wide: 7,
        offset: { slim: -0.16, wide: -0.15 },
    },
    large: {
        selector: "box_large",
        slim: 3,
        wide: 5,
        offset: { slim: -0.215, wide: -0.215 },
    },
};
export const START_ROW_POSITION = -0.25;

export const HELPER_BOX_SELECTOR = "heightCalculationBox";

// Конфигурация моделей
export const MODELS = [
    {
        name: "shelf",
        obj: "/assets/models/shelf.obj",
        mtl: "/assets/models/shelf.mtl",
    },
    {
        name: "row",
        obj: "/assets/models/row.obj",
        mtl: "/assets/models/row.mtl",
        position: { x: 0, y: 0.08, z: START_ROW_POSITION },
    },
];
