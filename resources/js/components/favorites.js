import Toastify from "toastify-js";

export default (() => {
    document.addEventListener("alpine:init", () => {
        Alpine.store("favorites", {
            list: Alpine.$persist([]).as("favorites"),
            getCount() {
                return this.list.filter((item) => item).length;
            },
            toggleProduct(id) {
                this.list[id] ? (this.list[id] = null) : (this.list[id] = true);
                Livewire.dispatch("favorites-updated", {
                    favorites: this.list,
                });
            },
        });
    });
})();
