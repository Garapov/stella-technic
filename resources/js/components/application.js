export default (() => {
    document.addEventListener("alpine:init", () => {
        Alpine.store("application", {
            windowWidth: Alpine.$persist(window.innerWidth).as("windowWidth"),
            init() {
                window.addEventListener('resize', () => {
                    this.windowWidth = window.innerWidth;
                    console.log(this.windowWidth);
                })
            }
        });
    });
})();
