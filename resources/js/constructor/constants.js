// Константы размеров
export const SHELF_HEIGHT = 1450; // мм
export const SCALE_FACTOR = 0.001;
export const ADDED_ROWS = [];
export const ROW_CONFIGS = {
    small: {
        height: 107.68,
        selector: "box",
        slim: 6,
        wide: 10,
        offset: { slim: -0.106, wide: -0.106 },
    },
    medium: {
        height: 155.55,
        selector: "box_medium",
        slim: 4,
        wide: 7,
        offset: { slim: -0.16, wide: -0.15 },
    },
    large: {
        height: 175,
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
        position: { x: 0, y: 0, z: START_ROW_POSITION },
    },
    {
        name: "wheel1",
        obj: "/assets/models/wheel1.obj",
        png: "/assets/models/wheel1.png",
        normal: "/assets/models/wheel1_Normal.png",
        height: "/assets/models/wheel1_Height.png",
        metallic: "/assets/models/wheel1_Metallic.png",
        roughness: "/assets/models/wheel1_Roughness.png",
        position: { x: 0.72, y: -0.45, z: -0.05 },
        scale: { x: 0.0008, y: 0.0008, z: 0.0008 },
        rotation: { x: 0, y: Math.PI / -2, z: 0 },
    },
    {
        name: "wheel1_copy",
        obj: "/assets/models/wheel1.obj",
        png: "/assets/models/wheel1.png",
        normal: "/assets/models/wheel1_Normal.png",
        height: "/assets/models/wheel1_Height.png",
        metallic: "/assets/models/wheel1_Metallic.png",
        roughness: "/assets/models/wheel1_Roughness.png",
        position: { x: 0.72, y: -0.45, z: -0.52 },
        scale: { x: 0.0008, y: 0.0008, z: 0.0008 },
        rotation: { x: 0, y: Math.PI / -2, z: 0 },
    },
    {
        name: "wheel2",
        obj: "/assets/models/wheel2.obj",
        png: "/assets/models/wheel2_BaseColor.png",
        normal: "/assets/models/wheel2_Normal.png",
        height: "/assets/models/wheel2_Height.png",
        metallic: "/assets/models/wheel2_Metallic.png",
        roughness: "/assets/models/wheel2_Roughness.png",
        position: { x: 0.035, y: -0.45, z: -0.72 },
        scale: { x: 0.0008, y: 0.0008, z: 0.0008 },
        rotation: { x: 0, y: Math.PI / -2, z: 0 },
    },
    {
        name: "wheel2_copy",
        obj: "/assets/models/wheel2.obj",
        png: "/assets/models/wheel2_BaseColor.png",
        normal: "/assets/models/wheel2_Normal.png",
        height: "/assets/models/wheel2_Height.png",
        metallic: "/assets/models/wheel2_Metallic.png",
        roughness: "/assets/models/wheel2_Roughness.png",
        position: { x: 0.035, y: -0.45, z: -0.245 },
        scale: { x: 0.0008, y: 0.0008, z: 0.0008 },
        rotation: { x: 0, y: Math.PI / -2, z: 0 },
    },
];
