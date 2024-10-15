<x-guest-layout>
    <div class="grid grid-cols-3 gap-4 mx-auto container">
        <div class="col-span-2">
            @livewire('main.slider')
        </div>
        <div class="flex flex-col gap-4">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://place-hold.it/700x320" alt="">
            </div>
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://place-hold.it/700x485" alt="">
            </div>
        </div>
    </div>

    @livewire('main.brands')
    @livewire('main.features')
    @livewire('main.popular')
    @livewire('main.articles')
    @livewire('main.customers')
    @livewire('main.news')
</x-guest-layout>