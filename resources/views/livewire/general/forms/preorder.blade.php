<div class="relative z-[80]">
    @if (setting('preorder'))
        <div tabindex="-1" aria-hΩidden="true" class="overflow-y-auto overflow-x-hidden bg-gray-900/50 dark:bg-gray-900/80 fixed top-0 right-0 left-0 bottom-0 z-60 flex justify-center items-center max-h-full" x-show="$store.application.forms.preorder" @click="$store.application.forms.preorder = false" x-cloak>
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                @livewire('general.preorderformblock', ['form_id' => setting('preorder')])
            </div>
        </div>
    @endif
</div>
