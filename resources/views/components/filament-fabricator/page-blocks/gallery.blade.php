@aware(['page'])
@php
    $lightbox_selector = "gallery-" . substr(bin2hex(openssl_random_pseudo_bytes(10 / 2)), 0, 10);
@endphp
<div class="px-4 py-4 md:py-8" x-data="{
        selector: `[data-fancybox='{{ $lightbox_selector }}']`,
        init() {
            window.fancybox.bind(this.selector);
        },
    }">
    <div class="container mx-auto">
        <div class="grid grid-cols-{{ $grid }} gap-4">
            @foreach ($images as $image)
                <div class="rounded-lg cursor-zoom-in" data-fancybox="{{ $lightbox_selector }}" data-src="{{ Storage::disk(config('filesystems.default'))->url($image) }}">
                    <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" class="w-full h-full object-cover object-center rounded-lg">
                </div>
            @endforeach
        </div>
    </div>
</div>
