
import Toastify from 'toastify-js';

export default (() => {
    document.addEventListener('alpine:init', () => {
        Alpine.store('cart', {
            list: Alpine.$persist([]).as('cart'),
                
            removeFromCart(productId) {
                this.list[productId] = null
            },
        
            addToCart({product, count = 0}) {
                console.log(product, count)
                    
                if  (this.list[product.id]) {
                    Toastify({
                        text: `${product.name} eже есть в корзине`,
                        duration: 3000,
                        destination: "https://github.com/apvarun/toastify-js",
                        newWindow: true,
                        close: true,
                        gravity: "bottom", // `top` or `bottom`
                        position: "right", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                          background: "linear-gradient(to right, rgb(209 6 6), rgb(224 114 0))",
                        },
                        onClick: function(){} // Callback after click
                    }).showToast();
                } else {
                    this.list[product.id] = {
                        ...product,
                        count: count,
                        variations: []
                    }
                    Toastify({
                        text: `${product.name} добавлен в корзину`,
                        duration: 3000,
                        destination: "https://github.com/apvarun/toastify-js",
                        newWindow: true,
                        close: true,
                        gravity: "bottom", // `top` or `bottom`
                        position: "right", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                          background: "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                        onClick: function(){} // Callback after click
                    }).showToast();
                }

                
            },
            addVariationToCart({product, variation, count = 0}) {
                if  (this.list[product.id] && this.list[product.id].variations[variation.id]) {
                    this.list[product.id].variations[variation.id] += +count;
                } else {
                    this.list[product.id] = {
                        ...product,
                        count: count,
                        variations: []
                    }
                    this.list[product.id].variations[variation.id] = {
                        ...variation,
                        count: count
                    };
                }
                Toastify({
                    text: `${variation.name} добавлен в корзину`,
                    duration: 3000,
                    destination: "https://github.com/apvarun/toastify-js",
                    newWindow: true,
                    close: true,
                    gravity: "bottom", // `top` or `bottom`
                    position: "right", // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    style: {
                      background: "linear-gradient(to right, #00b09b, #96c93d)",
                    },
                    onClick: function(){} // Callback after click
                }).showToast();
                
            },
            increase(productId) {
                this.list[productId].count++;
            },
            decrease(productId) {
                this.list[productId].count--;
                if (this.list[productId].count < 1) this.list[productId] = null;
            },
    
            getTotalPrice() {
                let price = 0.00;
                this.list.map(item => {
                    if (item != null) price += item.count * item.price;
                })
                return price.toFixed(2);
            },
            cartCount() {
                return this.list.filter(item => item != null).length;
            }
        });
    });
})();