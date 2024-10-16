<section class="py-10 dark:bg-dark">
    <div class="container mx-auto">
        <div class="flex items-center justify-between mb-10">
            <p class="text-4xl text-gray-900 dark:text-white">Популярные товары</p>
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-2.5 bg-blue-600 rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span>
                </div>
                <a href="#"
                    class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
                    Смотреть все
                    <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                </a>
            </div>
        </div>
        <div class="sm:hidden">
            <label for="tabs" class="sr-only">Select your country</label>
            <select id="tabs"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option>Тележки грузовые ручные</option>
                <option>Гидравлические тележки (рохли)</option>
                <option>Колеса и колесные опоры</option>
                <option>Пластиковые ящики V-1.2.3.4</option>
            </select>
        </div>
        <div class="hidden sm:flex gap-2 w-full">
            <button type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <x-fas-arrow-left class="w-5 h-5" />
            </button>
            <ul
                class="grow flex text-sm font-medium text-center text-gray-500 rounded-lg shadow dark:divide-gray-700 dark:text-gray-400">
                <li class="w-full focus-within:z-10">
                    <a href="#"
                        class="inline-block w-full p-4 text-gray-200 bg-blue-700 border-r border-blue-200 dark:border-blue-700 rounded-s-lg focus:ring-4 focus:ring-blue-300 active focus:outline-none dark:bg-blue-700 dark:text-white"
                        aria-current="page">Тележки грузовые ручные</a>
                </li>
                <li class="w-full focus-within:z-10">
                    <a href="#"
                        class="inline-block w-full p-4 bg-white border-r border-gray-200 dark:border-gray-700 hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:hover:text-white dark:bg-gray-700 dark:hover:bg-gray-700">Гидравлические
                        тележки (рохли)</a>
                </li>
                <li class="w-full focus-within:z-10">
                    <a href="#"
                        class="inline-block w-full p-4 bg-white border-r border-gray-200 dark:border-gray-700 hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:hover:text-white dark:bg-gray-700 dark:hover:bg-gray-700">Колеса
                        и колесные опоры</a>
                </li>
                <li class="w-full focus-within:z-10">
                    <a href="#"
                        class="inline-block w-full p-4 bg-white border-s-0 border-gray-200 dark:border-gray-700 rounded-e-lg hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:hover:text-white dark:bg-gray-700 dark:hover:bg-gray-700">Пластиковые
                        ящики V-1.2.3.4</a>
                </li>
            </ul>
            <button type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <x-fas-arrow-right class="w-5 h-5" />
            </button>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
            @livewire('general.product')
            @livewire('general.product')
            @livewire('general.product')
            @livewire('general.product')
        </div>
        <div class="flex justify-center">
            <nav aria-label="Page navigation example">
                <ul class="flex items-center -space-x-px h-8 text-sm">
                    <li>
                        <a href="#"
                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="sr-only">Previous</span>
                            <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 1 1 5l4 4" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</a>
                    </li>
                    <li>
                        <a href="#" aria-current="page"
                            class="z-10 flex items-center justify-center px-3 h-8 leading-tight text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">3</a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">4</a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">5</a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="sr-only">Next</span>
                            <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</section>
