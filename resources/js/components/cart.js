
export default (() => {
    document.addEventListener('alpine:init', () => {
        Alpine.store('cart', {
            list: Alpine.$persist([]).as('cart'),
                
            removeFromCart(productId) {
                this.list[productId] = null
            },
        
            addToCart({product, count = 0}) {
                console.log(product, count)
                    
                // if  (this.list[product.id]) {
                //     this.list[product.id].count += +count;
                // } else {
                //     this.list[product.id] = {
                //         ...product,
                //         count: count,
                //     }
                // }
            },
            addVariationToCart({product, variation, count = 0}) {
                // if  (this.list[product.id]) {
                //     this.list[product.id].count += +count;
                // } else {
                //     this.list[product.id] = {
                //         ...product,
                //         count: count,
                //         variation: variation,
                //     }
                // }
                console.log(product, variation, count);
                
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
            }
        });
    });
})();