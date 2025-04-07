<header>

    <div class="bg-white px-4 lg:px-6 py-2.5 dark:bg-gray-800 shadow-sm relative z-10">
        <div class="flex flex-wrap gap-8 justify-between items-center mx-auto container">
            @if (setting('site_logo'))
                <a href="/" class="flex items-center" wire:navigate>
                    <img src="{{ Storage::disk(config('filesystems.default'))->url(setting('site_logo')) }}" class="mr-3 h-6 sm:h-9 block"
                        alt="Stella-tech Logo" />
                </a>
            @endif
            @livewire('general.header.menu')
            <div class="flex gap-4 items-stretch">
                @if (setting('site_phone'))
                    <div class="flex flex-col items-end">
                        <a href="tel:88005514694" class="text-lg font-bold text-right text-gray-900 dark:text-white">{{ setting('site_phone') }}</a>
                        <div class="text-xs text-blue-600">Заказать звонок</div>
                    </div>
                    <div class="border border-gray-900"></div>
                @endif
                @if (setting('site_secondphone') && setting('site_worktime'))
                    <div class="flex flex-col">
                        <a href="tel:{{ setting('site_secondphone') }}" class="text-lg font-bold text-gray-900 dark:text-white">{{ setting('site_secondphone') }}</a>
                        <div class="text-xs text-gray-400">{{ setting('site_worktime') }}</div>
                    </div>
                @endif
                <livewire:general.header.auth />
            </div>
        </div>
    </div>

</header>
