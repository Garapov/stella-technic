// Константы размеров
export const SHELF_HEIGHT = 1450; // мм
export const ROW_HEIGHTS = { small: 110, medium: 158, large: 178 };
export const SCALE_FACTOR = 0.00101;
export const BASE_POSITIONS = { small: 0.176, medium: 0.213, large: 0.23 };
export const ROW_CONFIGS = {
    small: { selector: "box", slim: 6, wide: 10, offset: -0.106 },
    medium: { selector: "box_medium", slim: 4, wide: 7, offset: -0.16 },
    large: { selector: "box_large", slim: 3, wide: 5, offset: -0.215 },
};

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
        position: { x: 0, y: 0.08, z: -0.25 },
    },
];
