<div>
    @if (count($features) > 0)
        <section class="text-gray-600 body-font bg-gray-200 dark:bg-gray-700">
            <div class="container py-24 mx-auto">
                <div class="text-center mb-10">
                    <h1 class="md:text-4xl text-3xl font-medium text-center title-font text-gray-900 dark:text-gray-200 mb-2">
                        Более 30 лет</h1>
                    <p class="text-base dark:text-gray-400 leading-relaxed xl:w-2/4 lg:w-3/4 mx-auto">На рынке оборудования и
                        хранения для складов</p>
                </div>
                <div class="flex flex-wrap lg:w-4/5 sm:mx-auto sm:mb-2 -mx-2">
                    @foreach ($features as $feature)
                        <div class="p-2 sm:w-1/2 w-full">
                            <div class="bg-gray-100 rounded flex p-4 h-full items-center">
                                {{-- {{$feature->icon}} --}}
                                <div class="w-8 h-8 text-indigo-500 mr-4">
                                    {{ svg($feature->icon) }}
                                </div>
                                <span class="title-font font-medium">{{ $feature->text }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>



                <div class="grid grid-cols-4 gap-4 mt-20">
                    @foreach ($categories as $category)
                        <div class="grid grid-cols-4 gap-4 p-4 bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                            <img class="w-full rounded" src="{{ asset('storage/' . $category->image) }}" alt="Large avatar">
                            <div class="flex flex-col gap-3 col-span-3">
                                <a href="#" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    {{ $category->name }}
                                    <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                                </a>

                                @if ($category->categories)
                                    <ul>
                                        @foreach ($category->categories as $subcategory)
                                            <li><a href="#" class="text-xs text-gray-600 dark:text-gray-500 hover:underline">{{ $subcategory->name }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                </div>
                {{-- <div class="grid grid-cols-2 gap-4 mt-2">
                    <a href="#" class="inline-flex items-center justify-between px-1 py-1 pr-4 text-sm text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600" aria-label="Component requires Flowbite JavaScript">
                        <span aria-hidden="true" class="text-xs bg-blue-600 rounded-md text-white px-3 py-1.5 mr-3">
                            <x-fas-percent class="w-4 h-4"/>
                        </span>
                        <span class="text-sm font-medium">Распродажа / уценка / бу</span>
                        <x-fas-arrow-right class="w-3 h-3 ml-3" />
                    </a>
                    <a href="#" class="inline-flex items-center justify-between px-1 py-1 pr-4 text-sm text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600" aria-label="Component requires Flowbite JavaScript">
                        <span aria-hidden="true" class="text-xs bg-blue-600 rounded-md text-white px-3 py-1.5 mr-3">
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
