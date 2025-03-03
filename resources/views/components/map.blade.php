<div>
{{ $points }}
@php
    $string = Str::random(10);
@endphp
    <div class="rounded-lg overflow-hidden" x-data="{
        map: null,
        coordinates: [],
        address: '',
        data: '{{ $points }}',
        point: null,
        init() {
        [this.address, this.coordinates] = this.data.split('|');
        this.coordinates = JSON.parse(JSON.stringify([55.50779,37.755933]));
        
        console.log(this.address, this.coordinates);

        ymaps.ready(() => {
            this.map = new ymaps.Map('delivery-map-{{ $string }}', {
            center: this.coordinates,
            zoom: 13,
            controls: []
            });
            if (!this.point) {
                this.point = new ymaps.Placemark(this.coordinates);
                this.map.geoObjects.add(this.point);
            }
        });
        },
    }">
        <div class="w-full h-64" id="delivery-map-{{ $string }}"></div>
    </div>
    <!-- No surplus words or unnecessary actions. - Marcus Aurelius -->
</div>