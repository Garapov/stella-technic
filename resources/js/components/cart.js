import { data } from 'autoprefixer';
import Toastify from 'toastify-js';

export default (() => {
    document.addEventListener('alpine:init', () => {
        Alpine.store('cart', {
            list: Alpine.$persist([]).as('cart'),
            price: 0,
            discountedPrice: 0,
                
            removeFromCart(productId) {
                if (this.list[productId]) {
                    this.list[productId] = null;
                    
                    // Notify Livewire about the update
                    if (window.Livewire) {
                        window.Livewire.dispatch('cartUpdated', { 
                            products: this.list.filter(product => product !== null)
                        });
                    }
                }
            },

            addToCart({product, count = 0}) {
                console.log(product, count)
                
                // Find default variant
                const defaultVariant = product.variants.find(v => v.is_default);

                console.log('defaultVariant', defaultVariant);
                
                
                if (defaultVariant) {
                    this.addVariationToCart({
                        product: product,
                        variation: defaultVariant,
                        count: count
                    });
                } else {
                    this.list[product.id] = {
                        id: product.id,
                        name: product.name,
                        count: count,
                        variations: {}
                    };
                }
            },

            addVariationToCart({product, variation, count = 0}) {
                console.log('Adding variation to cart:', product, variation, count);

                try {
                    // Ensure the product exists in the cart
                    if (!this.list[product.id]) {
                        this.list[product.id] = {
                            id: product.id,
                            name: product.name,
                            count: 0,
                            variations: {}
                        };
                    }

                    // Add or update the variation
                    if (this.list[product.id].variations[variation.id]) {
                        // If variation exists, increment the count
                        this.list[product.id].variations[variation.id].count += (+count || 1);
                    } else {
                        // If variation doesn't exist, add it
                        this.list[product.id].variations[variation.id] = {
                            id: variation.id,
                            name: variation.name,
                            price: variation.price,
                            new_price: variation.new_price,
                            count: (+count || 1)
                        };
                    }

                    // Update total product count
                    this.list[product.id].count += (+count || 1);

                    // Notify Livewire to update products
                    if (window.Livewire) {
                        window.Livewire.dispatch('cartUpdated', { 
                            products: this.list.filter(product => product !== null)
                        });
                    }
                    
                    Toastify({
                        text: `${variation.name} добавлен в корзину`,
                        duration: 3000,
                        close: true,
                        gravity: "bottom",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                          background: "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                        onClick: function(){}
                    }).showToast();
                } catch (e) {
                    console.error('Error adding to cart:', e);
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
                        onClick: function(){}
                    }).showToast();
                }
            },

            increase(productId) {
                const item = this.list[productId];
                if (!item) return;

                item.count = (item.count || 0) + 1;
                
                // Update variation count as well
                const variations = Object.keys(item.variations || {});
                if (variations.length > 0) {
                    const variation = item.variations[variations[0]];
                    if (variation) {
                        variation.count = (variation.count || 0) + 1;
                    }
                }
                
                // Notify Livewire about the update
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated', { 
                        products: this.list.filter(product => product !== null)
                    });
                }
            },

            decrease(productId) {
                const item = this.list[productId];
                if (!item || !item.count || item.count <= 1) return;

                item.count--;
                
                // Update variation count as well
                const variations = Object.keys(item.variations || {});
                if (variations.length > 0) {
                    const variation = item.variations[variations[0]];
                    if (variation && variation.count > 1) {
                        variation.count--;
                    }
                }
                
                // Notify Livewire about the update
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated', { 
                        products: this.list.filter(product => product !== null)
                    });
                }
            },

            validateCount(productId) {
                const item = this.list[productId];
                if (!item) return;

                // Ensure count is a number and at least 1
                item.count = Math.max(1, parseInt(item.count) || 1);
                
                // Update variation count to match
                const variations = Object.keys(item.variations || {});
                if (variations.length > 0) {
                    const variation = item.variations[variations[0]];
                    if (variation) {
                        variation.count = item.count;
                    }
                }

                // Notify Livewire about the update
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated', { 
                        products: this.list.filter(product => product !== null)
                    });
                }
            },
    
            removeVariation(productId, variationId) {
                if (!this.list[productId] || !this.list[productId].variations[variationId]) return;
                
                const count = this.list[productId].variations[variationId].count;
                this.list[productId].count -= count;
                delete this.list[productId].variations[variationId];
                
                // If no variations left, remove the product
                if (Object.keys(this.list[productId].variations).length === 0) {
                    this.list[productId] = null;
                }
                
                // Notify Livewire about the update
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated', { 
                        products: this.list.filter(product => product !== null)
                    });
                }
            },

            increaseVariation(productId, variationId) {
                if (!this.list[productId] || !this.list[productId].variations[variationId]) return;
                
                this.list[productId].variations[variationId].count++;
                this.list[productId].count++;
                
                // Notify Livewire about the update
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated', { 
                        products: this.list.filter(product => product !== null)
                    });
                }
            },

            decreaseVariation(productId, variationId) {
                if (!this.list[productId] || !this.list[productId].variations[variationId]) return;
                if (this.list[productId].variations[variationId].count <= 1) {
                    this.removeVariation(productId, variationId);
                    return;
                }
                
                this.list[productId].variations[variationId].count--;
                this.list[productId].count--;
                
                // Notify Livewire about the update
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated', { 
                        products: this.list.filter(product => product !== null)
                    });
                }
            },

            validateVariationCount(productId, variationId) {
                if (!this.list[productId] || !this.list[productId].variations[variationId]) return;
                
                const variation = this.list[productId].variations[variationId];
                const oldCount = variation.count;
                variation.count = Math.max(1, parseInt(variation.count) || 1);
                
                // Update product total count
                this.list[productId].count += (variation.count - oldCount);
                
                // Notify Livewire about the update
                if (window.Livewire) {
                    window.Livewire.dispatch('cartUpdated', { 
                        products: this.list.filter(product => product !== null)
                    });
                }
            },
    
            getTotalPrice() {
                return this.list
                    .filter(item => item !== null)
                    .reduce((total, item) => {
                        const count = item?.count || 0;
                        const variations = Object.values(item?.variations || {});
                        const variation = variations[0];
                        const price = variation?.price || 0;
                        return total + (price * count);
                    }, 0);
            },

            getDiscountedPrice() {
                return this.list
                    .filter(item => item !== null)
                    .reduce((total, item) => {
                        const count = item?.count || 0;
                        const variations = Object.values(item?.variations || {});
                        const variation = variations[0];
                        const price = variation?.new_price || variation?.price || 0;
                        return total + (price * count);
                    }, 0);
            },

            cartCount() {
                return this.list
                    .filter(item => item !== null)
                    .reduce((total, item) => total + (item?.count || 0), 0);
            }
        });
    });
})();