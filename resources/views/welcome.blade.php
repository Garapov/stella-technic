<x-guest-layout>
    <div class="grid grid-cols-3 gap-4 mx-auto max-w-screen-xl">
        <div class="col-span-2">
            @livewire('main.slider')
        </div>
        <div class="flex flex-col gap-4">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image.jpg" alt="">
            </div>
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-2.jpg" alt="">
            </div>
        </div>
    </div>

    @livewire('main.brands')
    @livewire('main.features')
</x-guest-layout>