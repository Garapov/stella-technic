import Glide from '@glidejs/glide'

export default () => ({
    slider: new Glide('#main-slider', {
        autoplay: 5000,
    }).mount(),
    index: 0,
    init() {
        this.slider.on('move.after', () => {
            this.index = this.slider.index;
        })
    },
})