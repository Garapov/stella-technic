import { setupRowModels } from "./model-loader";
import { updateHeightCalculationBox } from "./model-loader";
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
        const model = three.scene.getObjectByName("models");
        const floor = three.scene.getObjectByName("floor");
        const clonedModel = three.scene.getObjectByName("clonedModels");
        const wheel1 = three.scene.getObjectByName("wheel1");
        const wheel1_copy = three.scene.getObjectByName("wheel1_copy");
        const wheel2 = three.scene.getObjectByName("wheel2");
        const wheel2_copy = three.scene.getObjectByName("wheel2_copy");
        const model_ground_plane = model.getObjectByName("ground_plane");
        const model_ground_plane_copy = model.getObjectByName("ground_plane_copy");
        const clonedModel_ground_plane = clonedModel.getObjectByName("ground_plane");
        const clonedModel_ground_plane_copy = clonedModel.getObjectByName("ground_plane_copy");
        
        if (!model) reject("Не нашлось модели на сцене");

        model.getObjectByName("leg_right").visible = true;
        model.getObjectByName("leg_left").visible = true;

        let tl = gsap.timeline({ repeat: 0 });

        tl.to(three.camera.position, {
            x: 0.332,
            y: 1.990,
            z: 2.887,
            duration: 1,
            ease: "power3.inOut",
        }, 0);
        tl.to(floor.position, {
            y: -5,
            duration: 1,
            ease: "power3.inOut",
        }, 0);

        [wheel1, wheel1_copy, wheel2, wheel2_copy].forEach(mesh => {
            // mesh.material.transparent = true;
            tl.to(mesh.position, {
                y: -0.45,
                duration: 1,
                ease: "power3.inOut",
            }, 0);
        });

        [model_ground_plane, model_ground_plane_copy, clonedModel_ground_plane, clonedModel_ground_plane_copy].forEach(mesh => {
            // mesh.material.transparent = true;
            tl.to(mesh.position, {
                y: -0.45,
                duration: 1,
                ease: "power3.inOut",
            }, 0);
        });

        tl.to(
            model.position,
            {
                z: 0,
                y: 0,
                duration: 1,
                ease: "power3.inOut",
                onComplete: () => {
                    resolve(true);
                    updateHeightCalculationBox(three, model);
                },
            },
            0.2,
        );
        tl.to(
            clonedModel.position,
            {
                y: 0,
                duration: 1,
                ease: "power3.inOut",
                onComplete: () => {
                    resolve(true);
                    updateHeightCalculationBox(three, model);
                },
            },
            0.2,
        );
    });
}
export function setPositionOnWall(three) {
    return new Promise((resolve, reject) => {
        const model = three.scene.getObjectByName("models");
        const clonedModel = three.scene.getObjectByName("clonedModels");
        const floor = three.scene.getObjectByName("floor");
        const wheel1 = three.scene.getObjectByName("wheel1");
        const wheel1_copy = three.scene.getObjectByName("wheel1_copy");
        const wheel2 = three.scene.getObjectByName("wheel2");
        const wheel2_copy = three.scene.getObjectByName("wheel2_copy");
        const model_ground_plane = model.getObjectByName("ground_plane");
        const model_ground_plane_copy = model.getObjectByName("ground_plane_copy");
        const clonedModel_ground_plane = clonedModel.getObjectByName("ground_plane");
        const clonedModel_ground_plane_copy = clonedModel.getObjectByName("ground_plane_copy");

        if (!model) reject("Не нашлось модели на сцене");

        model.getObjectByName("leg_right").visible = false;
        model.getObjectByName("leg_left").visible = false;

        let tl = gsap.timeline({ repeat: 0 });

        tl.to(model.position, {
            z: -1.2,
            y: 0,
            duration: 1,
            ease: "power3.inOut",
        }, 0);
        tl.to(floor.position, {
            y: -5,
            duration: 1,
            ease: "power3.inOut",
        }, 0);

        [wheel1, wheel1_copy, wheel2, wheel2_copy].forEach(mesh => {
            // mesh.material.transparent = true;
            tl.to(mesh.position, {
                y: -0.45,
                duration: 1,
                ease: "power3.inOut",
            }, 0);
        });

        [model_ground_plane, model_ground_plane_copy, clonedModel_ground_plane, clonedModel_ground_plane_copy].forEach(mesh => {
            // mesh.material.transparent = true;
            tl.to(mesh.position, {
                y: -0.45,
                duration: 1,
                ease: "power3.inOut",
            }, 0);
        });


        tl.to(
            three.camera.position,
            {
                x: 0.366,
                y: 0.525,
                z: 1.432,
                duration: 1,
                ease: "power3.inOut",
                onComplete: () => {
                    resolve(true);
                    updateHeightCalculationBox(three, model);
                },
            },
            0.2,
        );
    });
}

export function setPositionOnWheels(three) {
    return new Promise((resolve, reject) => {
        const floor = three.scene.getObjectByName("floor");
        const model = three.scene.getObjectByName("models");
        const clonedModel = three.scene.getObjectByName("clonedModels");
        const wheel1 = three.scene.getObjectByName("wheel1");
        const wheel1_copy = three.scene.getObjectByName("wheel1_copy");
        const wheel2 = three.scene.getObjectByName("wheel2");
        const wheel2_copy = three.scene.getObjectByName("wheel2_copy");
        const model_ground_plane = model.getObjectByName("ground_plane");
        const model_ground_plane_copy = model.getObjectByName("ground_plane_copy");
        const clonedModel_ground_plane = clonedModel.getObjectByName("ground_plane");
        const clonedModel_ground_plane_copy = clonedModel.getObjectByName("ground_plane_copy");

        if (!floor) reject("Не нашлось модели на сцене");


        let tl = gsap.timeline({ repeat: 0 });

        tl.to(floor.position, {
            y: -5.15,
            duration: 1,
            ease: "power3.inOut",
        }, 0);
        [wheel1, wheel1_copy, wheel2, wheel2_copy].forEach(mesh => {
            // mesh.material.transparent = true;
            // gsap.to(mesh.scale, {
            //     x: 0.0008,
            //     y: 0.0008,
            //     z: 0.0008,
            //     duration: 1,
            //     ease: "power3.inOut",
            // });
            tl.to(mesh.position, {
                y: -0.15,
                duration: 1,
                ease: "power3.inOut",
            }, 0);
        });

        [model_ground_plane, model_ground_plane_copy, clonedModel_ground_plane, clonedModel_ground_plane_copy].forEach(mesh => {
            // mesh.material.transparent = true;
            tl.to(mesh.position, {
                y: 0,
                duration: 1,
                ease: "power3.inOut",
            }, 0);
        });
        // gsap.to(clonedModel.position, {
        //     y: 0.15,
        //     duration: 1,
        //     ease: "power3.inOut",
        // });
        tl.to(
            three.camera.position,
            {
                x: 2,
                y: 0,
                z: 3,
                duration: 1,
                ease: "power3.inOut",
                onComplete: () => {
                    updateHeightCalculationBox(three, model);
                    resolve(true);
                },
            },
            0
        );
    });
}

export function changeDescHeight(three, height) {
    const models = three.scene.getObjectByName("models", true);
    const clonedModels = three.scene.getObjectByName("clonedModels", true);

    if (!models || !clonedModels) return;

    const objectsToAnimate = [
        "top",
        "top_clone",
        "side_plane_left_top_clone",
        "side_plane_right_top_clone",
        "plane_top_back",
        "plane_top_front",
        "bracing_left",
        "bracing_right",
        "rotationRightTopGroup",
        "rotationLeftTopGroup",
    ];
    const objectsToAnimateSlight = [
        "side_plane_left_top",
        "side_plane_right_top",
    ];
    const objectsToAnimateSlightest = [
        "side_plane_left_bottom",
        "side_plane_right_bottom",
    ];

    let tl = gsap.timeline({ repeat: 0 });
    // Анимация для обоих наборов моделей
    [models, clonedModels].forEach((modelSet) => {
        objectsToAnimateSlight.forEach((objectName) => {
            const object = modelSet.getObjectByName(objectName);
            if (object) {
                tl.to(
                    object.position,
                    {
                        y:
                            height === "high"
                                ? object.position.y + 0.07
                                : object.position.y - 0.07,
                        duration: 0.2,
                        ease: "power3.inOut",
                    },
                    0,
                );
            }
        });
        objectsToAnimateSlightest.forEach((objectName) => {
            const object = modelSet.getObjectByName(objectName);
            if (object) {
                tl.to(
                    object.position,
                    {
                        y:
                            height === "high"
                                ? object.position.y + 0.03
                                : object.position.y - 0.03,
                        duration: 0.2,
                        ease: "power3.inOut",
                    },
                    0,
                );
            }
        });
        objectsToAnimate.forEach((objectName) => {
            const object = modelSet.getObjectByName(objectName);
            if (object) {
                tl.to(
                    object.position,
                    {
                        y:
                            height === "high"
                                ? object.position.y + 0.47
                                : object.position.y - 0.47,
                        duration: 0.2,
                        ease: "power3.inOut",
                    },
                    0,
                );
            }
        });
    });

    return tl;
}

export function changeDescWidth(three, width) {
    const models = three.scene.getObjectByName("models", true);
    const clonedModels = three.scene.getObjectByName("clonedModels", true);

    if (!models || !clonedModels) return;

    let tl = gsap.timeline({ repeat: 0 });

    const moveLeftObjects = [
        "top",
        "side_plane_left_top",
        "side_plane_left_top_clone",
        "side_plane_left_bottom",
        "leg_bottom_left",
        "leg_left",
        "bottom_clone",
        "bracing_left",
        "bracing_bottom_left",
        "side_screw_left_1",
        "side_screw_left_2",
        "side_screw_left_3",
        "ground_plane_copy"
    ];

    const moveRightObjects = [
        "top_clone",
        "side_plane_right_top",
        "side_plane_right_top_clone",
        "side_plane_right_bottom",
        "leg_bottom_right",
        "leg_right",
        "bottom",
        "bracing_right",
        "bracing_bottom_right",
        "side_screw_right_1",
        "side_screw_right_2",
        "side_screw_right_3",
        "ground_plane"
    ];

    const moveTopObjects = [
        "rotationRightTopGroup",
        "rotationLeftTopGroup",
        "plane_top_back",
        "plane_top_front",
    ];

    const moveBottomObjects = [
        "rotationRightBottomGroup",
        "rotationLeftBottomGroup",
        "plane_bottom_back",
        "plane_bottom_front",
        "main_screw_bottom",
    ];

    tl.to(
        [three.scene.getObjectByName("wheel1").position, three.scene.getObjectByName("wheel1_copy").position],
        {
            x: width === "wide" ? 0.94 : 0.72,
            duration: 0.2,
            ease: "power3.inOut",
        },
        0,
    );
    

    tl.to(
        [three.scene.getObjectByName("wheel2").position, three.scene.getObjectByName("wheel2_copy").position],
        {
            x: width === "wide" ? -0.180 : 0.035,
            duration: 0.2,
            ease: "power3.inOut",
        },
        0,
    );

    // Применяем анимации для обоих наборов моделей
    [models, clonedModels].forEach((modelSet) => {
        // Анимация объектов, двигающихся влево
        moveLeftObjects.forEach((objectName) => {
            const object = modelSet.getObjectByName(objectName);
            if (object) {
                tl.to(
                    object.position,
                    {
                        x: width === "wide" ? -0.215 : 0,
                        duration: 0.2,
                        ease: "power3.inOut",
                    },
                    0,
                );
            }
        });

        // Анимация объектов, двигающихся вправо
        moveRightObjects.forEach((objectName) => {
            const object = modelSet.getObjectByName(objectName);
            if (object) {
                tl.to(
                    object.position,
                    {
                        x: width === "wide" ? 0.215 : 0,
                        duration: 0.2,
                        ease: "power3.inOut",
                    },
                    0,
                );
            }
        });

        moveTopObjects.forEach((objectName) => {
            const object = modelSet.getObjectByName(objectName);
            if (object) {
                tl.to(
                    object.position,
                    {
                        y:
                            width === "wide"
                                ? object.position.y + 0.165
                                : object.position.y - 0.165,
                        duration: 0.2,
                        ease: "power3.inOut",
                    },
                    0,
                );
            }
        });

        moveBottomObjects.forEach((objectName) => {
            const object = modelSet.getObjectByName(objectName);
            if (object) {
                tl.to(
                    object.position,
                    {
                        y:
                            width === "wide"
                                ? object.position.y - 0.165
                                : object.position.y + 0.165,
                        duration: 0.2,
                        ease: "power3.inOut",
                    },
                    0,
                );
            }
        });

        // Анимация поворота для специальных групп
        const rotationGroups = [
            {
                name: "rotationRightTopGroup",
                angle: width === "wide" ? Math.PI / -8 : 0,
            },
            {
                name: "rotationLeftTopGroup",
                angle: width === "wide" ? Math.PI / 8 : 0,
            },
            {
                name: "rotationLeftBottomGroup",
                angle: width === "wide" ? Math.PI / -8 : 0,
            },
            {
                name: "rotationRightBottomGroup",
                angle: width === "wide" ? Math.PI / 8 : 0,
            },
        ];

        rotationGroups.forEach((group) => {
            const object = modelSet.getObjectByName(group.name);
            if (object) {
                tl.to(
                    object.rotation,
                    {
                        z: group.angle,
                        duration: 0.2,
                        ease: "power3.inOut",
                    },
                    0,
                );
            }
        });
    });

    return tl;
}
