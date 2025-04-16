import { setupRowModels } from "./model-loader";
import gsap from "gsap";

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

export function setPositionOnFloor(three) {
    return new Promise((resolve, reject) => {
        const model = three.scene.getObjectByName('models');
        if (!model) reject('Не нашлось модели на сцене');

        model.getObjectByName('leg_right').visible = true;
        model.getObjectByName('leg_left').visible = true;

        let tl = gsap.timeline({repeat: 0});

        tl.to(three.camera.position, {
            x: 0.365,
            y: 0.759,
            z: 2.607,
            duration: 0.6,
            ease: "power3.inOut",
        });

        tl.to(model.position, {
            z: 0,
            y: 0,
            duration: 0.4,
            ease: "power3.inOut",
            onComplete: () => {resolve(true)},
        }, 0.2);
    });
    
}
export function setPositionOnWall(three) {
    return new Promise((resolve, reject) => {
        const model = three.scene.getObjectByName('models');
    
        if (!model) reject('Не нашлось модели на сцене');

        model.getObjectByName('leg_right').visible = false;
        model.getObjectByName('leg_left').visible = false;

        let tl = gsap.timeline({repeat: 0});

        tl.to(model.position, {
            z: -1.2,
            y: 0.2,
            duration: 0.4,
            ease: "power3.inOut",
        });
        tl.to(three.camera.position, {
            x: 0.365,
            y: 0.662,
            z: 0.81,
            duration: 0.6,
            ease: "power3.inOut",
            onComplete: resolve(true)
        }, 0.2);
    })

    
}
