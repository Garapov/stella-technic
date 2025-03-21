// import Alpine from 'alpinejs';
import Glide from "@glidejs/glide";
import Splide from "@splidejs/splide";
import { Fancybox } from "@fancyapps/ui";
import mask from "@alpinejs/mask";
// import persist from '@alpinejs/persist';

import dropdown from "./components/dropdown";
import mainSlider from "./components/main-slider";
import clientsSlider from "./components/clients";

import cart from "./components/cart";
import favorites from "./components/favorites";

window.glide = Glide;
window.splide = Splide;
window.fancybox = Fancybox;

// window.Alpine = Alpine;

document.addEventListener("alpine:init", () => {
    Alpine.data("dropdown", dropdown);
    Alpine.data("main_slider", mainSlider);
    Alpine.data("clients", clientsSlider);
});

document.addEventListener("livewire:init", () => {
    window.Livewire.on("cart-cleared", (event) => {
        Alpine.store("cart").list = [];
    });

    window.Livewire.on("favorites-cleared", (event) => {
        Alpine.store("favorites").list = [];
    });
});

document.addEventListener("articles_slider", (data) => {
    if (document.querySelector(".article_slider.glide .glide__slide")) {
        document
            .querySelectorAll(".article_slider.glide")
            .forEach((articles) => {
                new Glide(articles, {
                    autoplay: 5000,
                    perView: 3,
                    bound: true,
                }).mount();
            });
    }
});

document.addEventListener("init-map", (data) => {
    console.log("data", data);

    if (data.detail.delivery && data.detail.delivery.points) {
        console.log(`delivery-map-${data.detail.delivery.id}`);
        ymaps.ready(() => {
            setTimeout(() => {
                let map, point, coordinates, address;

                [address, coordinates] = data.detail.delivery.points.split("|");
                coordinates = coordinates.split(",");

                map = new ymaps.Map(
                    document.getElementById(
                        `delivery-map-${data.detail.delivery.id}`,
                    ),
                    {
                        center: coordinates,
                        zoom: 13,
                        controls: [],
                    },
                );
                if (!point) {
                    point = new ymaps.Placemark(coordinates);
                    map.geoObjects.add(point);
                }
            }, 1000);
        });
    }
});

Alpine.plugin(mask);
// Alpine.plugin(persist)

// Alpine.start();
