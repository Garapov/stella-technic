<x-guest-layout>
    {{-- <div class="grid grid-cols-3 gap-4 pt-8 mx-auto container">
        <div class="col-span-2">
            @livewire('main.slider')
        </div>
        <div class="flex flex-col gap-4">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://placehold.co/700x320" alt="">
            </div>
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://placehold.co/700x485" alt="">
            </div>
        </div>
    </div> --}}


    @livewire('main.slider')
    @livewire('main.features')
    @livewire('main.brands')
    {{-- @livewire('catalog.all') --}}
    {{-- @livewire('main.categories-in-block') --}}
    @livewire('main.popular')
    @livewire('main.certificates')
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>