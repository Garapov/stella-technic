export default () => ({
    isOpened: false,
    toggle() {
        this.isOpened = !this.isOpened;
    },
    close() {
        this.isOpened = false;
    }
})