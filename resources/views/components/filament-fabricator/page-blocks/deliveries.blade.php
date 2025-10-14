@aware(['page'])
@if ($deliveries->isNotEmpty())
<div class="py-10 md:py-8">
    <div class="container mx-auto flex flex-col gap-4">
        @foreach ($deliveries as $delivery)
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800" @if ($delivery->type == 'map' && $delivery->points)
                x-data="{
                    address: '{{ $delivery->points }}',
                    coordinates: [],
                    init() {
                        [address, coordinates] = this.address.split('|');
                        this.coordinates = coordinates.split(',');
                        this.initMap();
                    },
                    initMap() {
                        ymaps.ready(() => {
                            setTimeout(() => {
                                let map, point;

                                map = new ymaps.Map(
                                    document.getElementById(
                                        `delivery-map-{{ $delivery->id }}`,
                                    ),
                                    {
                                        center: this.coordinates,
                                        zoom: 13,
                                        controls: [],
                                    },
                                );
                                if (!point) {
                                    point = new ymaps.Placemark(this.coordinates);
                                    map.geoObjects.add(point);
                                }
                            }, 1000);
                        });
                    }
                }"
            
            @endif>
                <div class="text-xl mb-4">{{ $delivery->name }}</div>
                @switch($delivery->type)
                @case('map')
                    @if ($delivery->points)
                        <div class="w-full h-64" id="delivery-map-{{ $delivery->id }}" wire:ignore></div>
                    @endif
                    @break

                @case('text')
                    @if ($delivery->text)

                        <div class="text-sm text-gray-500 dark:text-gray-400 flex flex-col gap-2">
                        
                            {!! $delivery->text !!}
                        </div>

                    @endif
                    @break

                @case('delivery_systems')
                    @if($delivery->images)
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                            @foreach ($delivery->images as $image)
                                <div class="rounded-lg overflow-hidden">
                                    <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @break

                @endswitch
            </div>
        @endforeach
    </div>
</div>
@endif
