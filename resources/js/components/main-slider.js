import Glide from '@glidejs/glide'

export default () => ({
    slider: document.getElementById('main-slider') ? new Glide('#main-slider', {
        autoplay: 5000,
    }).mount() : null,
    index: 0,
    init() {    
        if (!this.slider) return;
        this.slider.on('move.after', () => {
            this.index = this.slider.index;
        })
    },
})