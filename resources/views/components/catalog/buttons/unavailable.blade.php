<div class="relative w-full md:w-auto" x-data="{ showTooltip: false }">
    <button type="button"
        @click.stop="@if (setting('open_deadlines')) {{ setting('open_deadlines') }} @endif $store.application.forms.deadlines = true; $store.application.one_click_variation = {{json_encode($variant)}}"
        @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
        class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium text-white focus:outline-none focus:ring-4 w-full md:w-auto bg-gray-500 hover:bg-gray-800 focus:ring-gray-300">
        <x-carbon-shopping-cart-plus class="h-5 w-5" />
        <span class="md:sr-only ms-2">Уточнить сроки</span>
    </button>
    <div x-show="showTooltip" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute bottom-full right-0 z-10 mb-2 rounded-md bg-slate-100 shadow px-3 py-2 text-sm font-medium text-slate-600 shadow-sm whitespace-nowrap">
        <div class="flex flex-col md:flex-row gap-2">
            Уточнить сроки
        </div>
        <div class="absolute -bottom-1 right-2 h-2 w-2 rotate-45 bg-slate-100 shadow"></div>
    </div>
</div>