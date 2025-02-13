import Glide from '@glidejs/glide'

export default () => ({
    slider: document.getElementById('clients-slider') ? new Glide('#clients-slider', {
        autoplay: 5000,
        perView: 6,
        bound: true
    }).mount() : null,
    index: 0,
    init() {
        if (!this.slider) return;
        
        this.slider.on('move.after', () => {
            this.index = this.slider.index;
        })
    },
})