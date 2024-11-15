import Glide from '@glidejs/glide'

export default () => ({
    slider: new Glide('#clients-slider', {
        autoplay: 5000,
        perView: 6,
        bound: true
    }).mount(),
    index: 0,
    init() {
        this.slider.on('move.after', () => {
            this.index = this.slider.index;
        })
    },
})