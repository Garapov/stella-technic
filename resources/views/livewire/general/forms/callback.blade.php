<div>
    @if (setting('callback'))
        <div tabindex="-1" aria-hΩidden="true" class="overflow-y-auto overflow-x-hidden bg-gray-900/50 dark:bg-gray-900/80 fixed top-0 right-0 left-0 bottom-0 z-50 flex justify-center items-center max-h-full" x-show="$store.application.forms.callback" @click="$store.application.forms.callback = false" x-cloak>
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                @livewire('general.modalformblock', ['form_id' => setting('callback'), 'form_name' => 'callback'])
            </div>
        </div>
    @endif
</div>
