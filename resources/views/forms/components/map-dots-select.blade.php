<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>

    <div x-data="{
        state: $wire.$entangle('{{ $getStatePath() }}'),
        map: null,
        init() {
           this.state = JSON.stringify({
                points: [],
            });
            ymaps.ready(this.initMap);
        },
        destroy() {},
        initMap() {
            this.map = new ymaps.Map($refs.map, {
                center: [55.76, 37.64],
                zoom: 13,
                controls: []
            });

            let addPointToMap = (coords) => {
                this.map.geoObjects.add(new ymaps.Placemark(coords));
                let state = JSON.parse(this.state);
                state.points.push(coords);
                this.state = JSON.stringify(state);
                this.map.setBounds(this.map.geoObjects.getBounds(), {checkZoomRange:true});
            };

            this.map.events.add('click', function (e) {
                addPointToMap(e.get('coords'));
            });


            var searchControl = new ymaps.control.SearchControl({
                options: {
                    // Задает поиск только по топонимам.
                    provider: 'yandex#search'
                }
            });

            searchControl.search('Варшавское шоссе').then(function (res) {});

            searchControl.events.add('resultselect', function(e) {
                var index = e.get('index');
                searchControl.getResult(index).then(function(res) {
                    addPointToMap(res.geometry.getCoordinates());
                });
            })

            this.map.controls.add(searchControl);

        
        },

        addPointToMap(coords) {
            
        }
    }">
        <div class="w-full h-96 rounded-lg overflow-hidden" x-ref="map"></div>

        <div x-text="JSON.stringify(state)"></div>
    </div>
</x-dynamic-component>
