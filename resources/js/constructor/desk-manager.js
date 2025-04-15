import { setupRowModels } from "./model-loader";

export function addDeskClone(three) {
    let shelf = three.scene.getObjectByName(`models`, true);
    if (!shelf) return;

    let clonedShelf = shelf.clone();
    clonedShelf.visible = false;
    clonedShelf.name = "clonedModels";
    clonedShelf.rotation.y = Math.PI;
    clonedShelf.position.z = -0.5625;
    clonedShelf.position.x = 0.7295;
    three.scene.add(clonedShelf);

    console.log(three.scene.children);

    // Специальная обработка для row модели
    setupRowModels(three);
}
export function changeDeskCloneVisibility(three, visibility) {
    let clonedShelf = three.scene.getObjectByName(`clonedModels`, true);
    if (!clonedShelf) return;

    clonedShelf.visible = visibility;
}
