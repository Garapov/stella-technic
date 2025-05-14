import { data } from "autoprefixer";
import Toastify from "toastify-js";

export default (() => {
    document.addEventListener("alpine:init", () => {
        Alpine.store("cart", {
            list: Alpine.$persist([]).as("cart"),
            constructor: Alpine.$persist([]).as("constructor"),
            price: 0,
            discountedPrice: 0,

            cartCount() {
                return this.list.filter((item) => item !== null).length + this.constructor.filter((item) => item !== null).length;
            },

            removeFromCart(productId) {
                if (this.list[productId]) {
                    this.list[productId] = null;
                }
            },
            removeConstructFromCart(productId) {
                if (this.constructor[productId]) {
                    this.constructor[productId] = null;
                }
            },

            addVariationToCart({ variationId, count = 0, name }) {

                try {
                    // Ensure the product exists in the cart
                    if (!this.list[variationId]) {
                        this.list[variationId] = +count || 1;
                    } else {
                        this.list[variationId] += +count || 1;
                    }

                    Toastify({
                        text: `${name} добавлен в корзину`,
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background:
                                "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                        onClick: function () {},
                    }).showToast();
                } catch (e) {
                    console.error("Error adding to cart:", e);
                    Toastify({
                        text: `Что-то пошло не так, попробуйте еще раз или перезагрузите страницу.`,
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, red, red)",
                        },
                        onClick: function () {},
                    }).showToast();
                }
            },

            addConstructionToCart(product) {
                return new Promise((resolve, reject) => {
                    try {
                        this.constructor[product.id] = product;
                        Toastify({
                            text: `${product.name} добавлен в корзину`,
                            duration: 3000,
                            close: true,
                            gravity: "bottom",
                            position: "right",
                            stopOnFocus: true,
                            style: {
                                background:
                                    "linear-gradient(to right, #00b09b, #96c93d)",
                            },
                            onClick: function () {},
                        }).showToast();
                        resolve(true);
                    } catch(error) {
                        reject(error);
                    }
                }) 
            },

            increase(productId) {
                if (!this.list[productId]) return;

                this.list[productId] = +this.list[productId] + 1;
            },

            decrease(productId) {
                if (!this.list[productId] || +this.list[productId] <= 1) return;

                this.list[productId] = +this.list[productId] - 1;
            },
        });
    });
})();
