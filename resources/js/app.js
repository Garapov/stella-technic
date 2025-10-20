// import Alpine from 'alpinejs';
import Glide from "@glidejs/glide";
import Splide from "@splidejs/splide";
import { Fancybox } from "@fancyapps/ui";
import mask from "@alpinejs/mask";
import rangeSlider from "range-slider-input";
import Toastify from "toastify-js";
// import 'toolcool-range-slider/dist/plugins/tcrs-marks.min.js';
// import 'toolcool-range-slider';
// import sort from '@alpinejs/sort'
// import persist from '@alpinejs/persist';

import dropdown from "./components/dropdown";
import constructor from "./constructor/main";
import mainSlider from "./components/main-slider";
import clientsSlider from "./components/clients";
import brandsSlider from "./components/brands";

import cart from "./components/cart";
import favorites from "./components/favorites";
import application from "./components/application";
import recently from "./components/recently";




window.glide = Glide;
window.splide = Splide;
window.fancybox = Fancybox;
window.rangeSlider = rangeSlider;
window.toastify = Toastify;

// window.Alpine = Alpine;

document.addEventListener("alpine:init", () => {
    Alpine.data("dropdown", dropdown);
    Alpine.data("construct", constructor);
    Alpine.data("main_slider", mainSlider);
    Alpine.data("clients", clientsSlider);
    Alpine.data("brands", brandsSlider);
});



// alert('Разрешение экрана ' + window.innerWidth + 'x' + window.innerHeight)

document.addEventListener("livewire:init", () => {
    window.Livewire.on("cart-cleared", (event) => {
        Alpine.store("cart").list = [];
        Alpine.store("cart").constructor = [];
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
                    breakpoints: {
                        1024: {
                            perView: 2
                        },
                        800: {
                            perView: 1
                        }
                    }
                }).mount();
            });
    }
});

// document.addEventListener("init-map", (data) => {

//     console.log('data', data);

//     if (data.detail.delivery && data.detail.delivery.points) {
//         ymaps.ready(() => {
//             setTimeout(() => {
//                 let map, point, coordinates, address;

//                 [address, coordinates] = data.detail.delivery.points.split("|");
//                 coordinates = coordinates.split(",");

//                 map = new ymaps.Map(
//                     document.getElementById(
//                         `delivery-map-${data.detail.delivery.id}`,
//                     ),
//                     {
//                         center: coordinates,
//                         zoom: 13,
//                         controls: [],
//                     },
//                 );
//                 if (!point) {
//                     point = new ymaps.Placemark(coordinates);
//                     map.geoObjects.add(point);
//                 }
//             }, 1000);
//         });
//     }
// });


document.addEventListener("cache-cleared", (data) => {
    Toastify({
        text: `Кеш страницы очищен`,
        duration: 3000,
        close: true,
        gravity: "bottom",
        position: "right",
        stopOnFocus: true,
        style: {
            background:
                "linear-gradient(to right, #00b09b,rgb(94, 35, 117))",
        },
        onClick: function () {},
    }).showToast();
});

Alpine.plugin(mask);
// Alpine.plugin(uI);
// Alpine.plugin(sort)

// Alpine.start();
