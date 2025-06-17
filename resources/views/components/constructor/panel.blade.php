@if (auth()->user() && auth()->user()->hasRole('super_admin'))
    <div class="absolute top-0 left-0 w-full h-full bg-white dark:bg-gray-700 p-4 transition-transform overflow-auto" x-bind:class="!panelOpened ? 'translate-y-full' : 'translate-y-0'">
        <div class="absolute top-4 right-4  cursor-pointer" @click="closePanel">
            <x-carbon-close-large class="w-6 h-6" />
        </div>

        <form wire:submit="createVariation">
            {{ $this->form }}


            <button type="submit" class="w-full text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mt-4 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Создать</button>
        </form>

        <x-filament-actions::modals />
    </div>
@endif