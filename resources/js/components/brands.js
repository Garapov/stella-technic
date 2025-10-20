import Glide from '@glidejs/glide'

export default () => ({
    slider: document.getElementById('brands-slider') ? new Glide('#brands-slider', {
        autoplay: 5000,
        perView: 6,
        bound: true,
        breakpoints: {
            1280: {
                perView: 4
            },
            1024: {
                perView: 2
            },
            800: {
                perView: 1
            }
        }
    }).mount() : null,
    index: 0,
    init() {
        if (!this.slider) return;
        
        this.slider.on('move.after', () => {
            this.index = this.slider.index;
        })
    },
})