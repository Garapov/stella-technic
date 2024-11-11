// import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';

import dropdown from './components/dropdown';

// window.Alpine = Alpine;


document.addEventListener('alpine:init', () => {
    Alpine.data('dropdown', dropdown);
});



Alpine.plugin(mask);

// Alpine.start();