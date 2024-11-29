// import Alpine from 'alpinejs';
import Glide from '@glidejs/glide'
import mask from '@alpinejs/mask';
// import persist from '@alpinejs/persist';

import dropdown from './components/dropdown';
import mainSlider from './components/main-slider';
import clientsSlider from './components/clients';

// import cart from './components/cart';

window.glide = Glide;

// window.Alpine = Alpine;


document.addEventListener('alpine:init', () => {
    Alpine.data('dropdown', dropdown);
    Alpine.data('main_slider', mainSlider);
    Alpine.data('clients', clientsSlider);
});



Alpine.plugin(mask);
// Alpine.plugin(persist)

// Alpine.start();