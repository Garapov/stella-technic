export default (() => {
    document.addEventListener("alpine:init", () => {
        Alpine.store("recently", {
            list: Alpine.$persist([]).as("recently"),
            toggleProduct(id) {
                if (!this.list.includes(id)) {
                    this.list.push(id)
                }
            },
        });
    });
})();
