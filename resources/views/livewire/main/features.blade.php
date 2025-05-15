<div>
    @if (count($features) > 0)
        <section class="text-gray-600 body-font bg-slate-50 dark:bg-gray-700">
            <div class="container py-10 mx-auto">
                <div class="text-center mb-10">
                    <h1 class="md:text-4xl text-3xl font-bold text-center title-font mb-2"><span class="text-blue-500">Более 30 лет</span> <span class="text-slate-700">на рынке оборудования и
                        хранения для складов</span></h1>
                </div>
                <div class="grid grid-cols-5 gap-4">
                    @foreach ($features as $feature)
                        <div class="bg-slate-100 rounded flex p-4 h-full items-center">
                            <div class="min-w-12 min-h-12 w-12 h-12 text-indigo-500 mr-4">
                                <img src="{{ Storage::disk(config("filesystems.default"))->url($feature->icon) }}" alt="">
                            </div>
                            <span class="title-font font-medium">{{ $feature->text }}</span>
                        </div>
                    @endforeach
                </div>



                <div class="grid grid-cols-4 gap-4 mt-10">
                    @foreach ($categories as $category)
                        @if ($category->variationsCount() < 1)
                            @continue
                        @endif
                        @livewire('general.category', [
                            'category' => $category,
                        ], key($category->id))
                    @endforeach
                    
                </div>
                {{-- <div class="grid grid-cols-2 gap-4 mt-2">
                    <a href="#" class="inline-flex items-center justify-between px-1 py-1 pr-4 text-sm text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600" aria-label="Component requires Flowbite JavaScript">
                        <span aria-hidden="true" class="text-xs bg-blue-500 rounded-md text-white px-3 py-1.5 mr-3">
                            <x-fas-percent class="w-4 h-4"/>
                        </span>
                        <span class="text-sm font-medium">Распродажа / уценка / бу</span>
                        <x-fas-arrow-right class="w-3 h-3 ml-3" />
                    </a>
                    <a href="#" class="inline-flex items-center justify-between px-1 py-1 pr-4 text-sm text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600" aria-label="Component requires Flowbite JavaScript">
                        <span aria-hidden="true" class="text-xs bg-blue-500 rounded-md text-white px-3 py-1.5 mr-3">
                            <x-fas-gear class="w-4 h-4"/>
                        </span>
                        <span class="text-sm font-medium">Сервис и услуги</span>
                        <x-fas-arrow-right class="w-3 h-3 ml-3" />
                    </a> --}}
                </div>
            </div>
        </section>
    @endIf
</div>
