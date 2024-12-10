export default {
    list: [],

    init() {
        this.list = this.getFavorites();
        this.updateFavoritesCount();
    },

    getFavorites() {
        const favorites = localStorage.getItem('favorites');
        return favorites ? JSON.parse(favorites) : [];
    },

    saveFavorites() {
        localStorage.setItem('favorites', JSON.stringify(this.list));
        this.updateFavoritesCount();
        Livewire.dispatch('favorites-updated', { favorites: this.list });
    },

    toggleProduct(productId) {
        productId = productId.toString();
        const index = this.list.indexOf(productId);
        
        if (index === -1) {
            this.list.push(productId);
        } else {
            this.list.splice(index, 1);
        }
        
        this.saveFavorites();
    },

    updateFavoritesCount() {
        document.querySelectorAll('[data-favorites-count]').forEach(counter => {
            counter.textContent = this.list.length || '';
        });
    },

    getCount() {
        return this.list.length;
    },

    includes(productId) {
        return this.list.includes(productId.toString());
    }
} 