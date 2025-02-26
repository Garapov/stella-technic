
<div class="grid grid-cols-4 gap-4 bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 p-4">
    <div class="col-span-1 flex items-start py-4">
        <img class="w-full h-auto aspect-square object-cover object-center rounded-full" src="{{ asset('storage/' . $worker->image) }}" alt="{{ $worker->name }}">
    </div>
    <div class="flex flex-col gap-4 justify-between leading-normal col-span-3">
        <div class="flex flex-col gap-4">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $worker->name }}</h5>
            <div class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                {{ Str::limit($worker->description, 160) }}
                <a href="#" class="inline-flex items-center font-medium text-blue-600 text-sm dark:text-blue-500">
                    Подробнее
                    <x-carbon-arrow-right class="w-3 h-3 ms-1 rtl:rotate-180" />
                </a>
            </div>
        </div>
        @if ($worker->phone || $worker->email)
            <div class="flex items-start gap-8">
                @if ($worker->phone)
                    <a href="tel:{{ $worker->phone }}" class="inline-flex items-center gap-1 font-medium text-blue-600 dark:text-blue-500 text-lg">
                        <x-carbon-phone class="w-6 h-6" />
                        {{ $worker->phone }}
                        @if ($worker->phone_ext)
                            <span class="text-xs">Доп.{{ $worker->phone_ext }}</span>
                        @endif
                    </a>
                @endif
                @if ($worker->email)
                    <a href="mailto:{{ $worker->email }}" class="inline-flex items-center gap-1 font-medium text-blue-600 dark:text-blue-500 text-lg">
                        <x-carbon-email class="w-6 h-6" />
                        {{ $worker->email }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
