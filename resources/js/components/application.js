export default (() => {
    document.addEventListener("alpine:init", () => {
        Alpine.store("application", {
            windowWidth: Alpine.$persist(window.innerWidth).as("windowWidth"),
            forms: {
                callback: false,
                buy_one_click: false,
            },
            one_click_variation: null,
            burger: false,
            init() {
                window.addEventListener('resize', () => {
                    this.windowWidth = window.innerWidth;
                    console.log(this.windowWidth);
                })
            }
        });
    });
})();