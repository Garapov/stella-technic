// import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';

import dropdown from './components/dropdown';
import mainSlider from './components/main-slider';

// window.Alpine = Alpine;


document.addEventListener('alpine:init', () => {
    Alpine.data('dropdown', dropdown);
    Alpine.data('main_slider', mainSlider);
});



Alpine.plugin(mask);

// Alpine.start();