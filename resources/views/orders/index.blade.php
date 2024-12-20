<x-profile-layout>
    <div class="sm:px-6 lg:px-8">
        <div class="w-full space-y-8 sm:px-4 lg:px-0">

            <div class="w-full border-t border-b border-gray-200 bg-white shadow-sm sm:rounded-lg sm:border">
                <h3 class="sr-only">Order placed on <time datetime="2021-07-06">Jul 6, 2021</time></h3>

                <div
                    class="flex items-center border-b border-gray-200 p-4 sm:grid sm:grid-cols-4 sm:gap-x-6 sm:p-6">
                    <dl
                        class="grid flex-1 grid-cols-2 gap-x-6 text-sm sm:col-span-3 sm:grid-cols-3 lg:col-span-2">
                        <div>
                            <dt class="font-medium text-gray-900">Order number</dt>
                            <dd class="mt-1 text-gray-500">WU88191111</dd>
                        </div>
                        <div class="hidden sm:block">
                            <dt class="font-medium text-gray-900">Date placed</dt>
                            <dd class="mt-1 text-gray-500">
                                <time datetime="2021-07-06">Jul 6, 2021</time>
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-900">Total amount</dt>
                            <dd class="mt-1 font-medium text-gray-900">$160.00</dd>
                        </div>
                    </dl>

                    <div x-data="Components.menu({ open: false })" x-init="init()"
                        @keydown.escape.stop="open = false; focusButton()" @click.away="onClickAway($event)"
                        class="relative flex justify-end lg:hidden">
                        <div class="flex items-center">
                            <button type="button"
                                class="-m-2 flex items-center p-2 text-gray-400 hover:text-gray-500"
                                id="menu-0-button" x-ref="button" @click="onButtonClick()"
                                @keyup.space.prevent="onButtonEnter()" @keydown.enter.prevent="onButtonEnter()"
                                aria-expanded="false" aria-haspopup="true"
                                x-bind:aria-expanded="open.toString()" @keydown.arrow-up.prevent="onArrowUp()"
                                @keydown.arrow-down.prevent="onArrowDown()">
                                <span class="sr-only">Options for order WU88191111</span>
                                <svg class="h-6 w-6" x-description="Heroicon name: outline/ellipsis-vertical"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z">
                                    </path>
                                </svg>
                            </button>
                        </div>


                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 z-10 mt-2 w-40 origin-bottom-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            x-ref="menu-items" x-description="Dropdown menu, show/hide based on menu state."
                            x-bind:aria-activedescendant="activeDescendant" role="menu"
                            aria-orientation="vertical" aria-labelledby="menu-0-button" tabindex="-1"
                            @keydown.arrow-up.prevent="onArrowUp()" @keydown.arrow-down.prevent="onArrowDown()"
                            @keydown.tab="open = false" @keydown.enter.prevent="open = false; focusButton()"
                            @keyup.space.prevent="open = false; focusButton()" style="display: none;">
                            <div class="py-1" role="none">
                                <a href="#" class="text-gray-700 block px-4 py-2 text-sm" x-state:on="Active"
                                    x-state:off="Not Active"
                                    :class="{ 'bg-gray-100 text-gray-900': activeIndex === 0, 'text-gray-700': !(activeIndex === 0) }"
                                    role="menuitem" tabindex="-1" id="menu-0-item-0"
                                    @mouseenter="onMouseEnter($event)" @mousemove="onMouseMove($event, 0)"
                                    @mouseleave="onMouseLeave($event)"
                                    @click="open = false; focusButton()">View</a>
                                <a href="#" class="text-gray-700 block px-4 py-2 text-sm"
                                    :class="{ 'bg-gray-100 text-gray-900': activeIndex === 1, 'text-gray-700': !(activeIndex === 1) }"
                                    role="menuitem" tabindex="-1" id="menu-0-item-1"
                                    @mouseenter="onMouseEnter($event)" @mousemove="onMouseMove($event, 1)"
                                    @mouseleave="onMouseLeave($event)"
                                    @click="open = false; focusButton()">Invoice</a>
                            </div>
                        </div>

                    </div>

                    <div class="hidden lg:col-span-2 lg:flex lg:items-center lg:justify-end lg:space-x-4">
                        <a href="#"
                            class="flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span>View Order</span>
                            <span class="sr-only">WU88191111</span>
                        </a>
                        <a href="#"
                            class="flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span>View Invoice</span>
                            <span class="sr-only">for order WU88191111</span>
                        </a>
                    </div>
                </div>

                <!-- Products -->
                <h4 class="sr-only">Items</h4>
                <ul role="list" class="divide-y divide-gray-200">

                    <li class="p-4 sm:p-6">
                        <div class="flex items-center sm:items-start">
                            <div
                                class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 sm:h-40 sm:w-40">
                                <img src="https://placehold.co/160x160"
                                    alt="Moss green canvas compact backpack with double top zipper, zipper front pouch, and matching carry handle and backpack straps."
                                    class="h-full w-full object-cover object-center">
                            </div>
                            <div class="ml-6 flex-1 text-sm">
                                <div class="font-medium text-gray-900 sm:flex sm:justify-between">
                                    <h5>Micro Backpack</h5>
                                    <p class="mt-2 sm:mt-0">$70.00</p>
                                </div>
                                <p class="hidden text-gray-500 sm:mt-2 sm:block">Are you a minimalist looking
                                    for a compact carry option? The Micro Backpack is the perfect size for your
                                    essential everyday carry items. Wear it like a backpack or carry it like a
                                    satchel for all-day use.</p>
                            </div>
                        </div>

                        <div class="mt-6 sm:flex sm:justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-500"
                                    x-description="Heroicon name: mini/check-circle"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p class="ml-2 text-sm font-medium text-gray-500">Delivered on <time
                                        datetime="2021-07-12">July 12, 2021</time></p>
                            </div>

                            <div
                                class="mt-6 flex items-center space-x-4 divide-x divide-gray-200 border-t border-gray-200 pt-4 text-sm font-medium sm:mt-0 sm:ml-4 sm:border-none sm:pt-0">
                                <div class="flex flex-1 justify-center">
                                    <a href="#"
                                        class="whitespace-nowrap text-indigo-600 hover:text-indigo-500">View
                                        product</a>
                                </div>
                                <div class="flex flex-1 justify-center pl-4">
                                    <a href="#"
                                        class="whitespace-nowrap text-indigo-600 hover:text-indigo-500">Buy
                                        again</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="p-4 sm:p-6">
                        <div class="flex items-center sm:items-start">
                            <div
                                class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 sm:h-40 sm:w-40">
                                <img src="https://placehold.co/160x160"
                                    alt="Bright yellow canvas tote with double-stitched straps, handle, and matching zipper."
                                    class="h-full w-full object-cover object-center">
                            </div>
                            <div class="ml-6 flex-1 text-sm">
                                <div class="font-medium text-gray-900 sm:flex sm:justify-between">
                                    <h5>Nomad Shopping Tote</h5>
                                    <p class="mt-2 sm:mt-0">$90.00</p>
                                </div>
                                <p class="hidden text-gray-500 sm:mt-2 sm:block">This durable shopping tote is
                                    perfect for the world traveler. Its yellow canvas construction is water,
                                    fray, tear resistant. The matching handle, backpack straps, and shoulder
                                    loops provide multiple carry options for a day out on your next adventure.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 sm:flex sm:justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-500"
                                    x-description="Heroicon name: mini/check-circle"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p class="ml-2 text-sm font-medium text-gray-500">Delivered on <time
                                        datetime="2021-07-12">July 12, 2021</time></p>
                            </div>

                            <div
                                class="mt-6 flex items-center space-x-4 divide-x divide-gray-200 border-t border-gray-200 pt-4 text-sm font-medium sm:mt-0 sm:ml-4 sm:border-none sm:pt-0">
                                <div class="flex flex-1 justify-center">
                                    <a href="#"
                                        class="whitespace-nowrap text-indigo-600 hover:text-indigo-500">View
                                        product</a>
                                </div>
                                <div class="flex flex-1 justify-center pl-4">
                                    <a href="#"
                                        class="whitespace-nowrap text-indigo-600 hover:text-indigo-500">Buy
                                        again</a>
                                </div>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>

            <div class="w-full border-t border-b border-gray-200 bg-white shadow-sm sm:rounded-lg sm:border">
                <h3 class="sr-only">Order placed on <time datetime="2020-12-22">Dec 22, 2020</time></h3>

                <div
                    class="flex items-center border-b border-gray-200 p-4 sm:grid sm:grid-cols-4 sm:gap-x-6 sm:p-6">
                    <dl
                        class="grid flex-1 grid-cols-2 gap-x-6 text-sm sm:col-span-3 sm:grid-cols-3 lg:col-span-2">
                        <div>
                            <dt class="font-medium text-gray-900">Order number</dt>
                            <dd class="mt-1 text-gray-500">AT48441546</dd>
                        </div>
                        <div class="hidden sm:block">
                            <dt class="font-medium text-gray-900">Date placed</dt>
                            <dd class="mt-1 text-gray-500">
                                <time datetime="2020-12-22">Dec 22, 2020</time>
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-900">Total amount</dt>
                            <dd class="mt-1 font-medium text-gray-900">$40.00</dd>
                        </div>
                    </dl>

                    <div x-data="Components.menu({ open: false })" x-init="init()"
                        @keydown.escape.stop="open = false; focusButton()" @click.away="onClickAway($event)"
                        class="relative flex justify-end lg:hidden">
                        <div class="flex items-center">
                            <button type="button"
                                class="-m-2 flex items-center p-2 text-gray-400 hover:text-gray-500"
                                id="menu-1-button" x-ref="button" @click="onButtonClick()"
                                @keyup.space.prevent="onButtonEnter()" @keydown.enter.prevent="onButtonEnter()"
                                aria-expanded="false" aria-haspopup="true"
                                x-bind:aria-expanded="open.toString()" @keydown.arrow-up.prevent="onArrowUp()"
                                @keydown.arrow-down.prevent="onArrowDown()">
                                <span class="sr-only">Options for order AT48441546</span>
                                <svg class="h-6 w-6" x-description="Heroicon name: outline/ellipsis-vertical"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z">
                                    </path>
                                </svg>
                            </button>
                        </div>


                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 z-10 mt-2 w-40 origin-bottom-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            x-ref="menu-items" x-description="Dropdown menu, show/hide based on menu state."
                            x-bind:aria-activedescendant="activeDescendant" role="menu"
                            aria-orientation="vertical" aria-labelledby="menu-1-button" tabindex="-1"
                            @keydown.arrow-up.prevent="onArrowUp()" @keydown.arrow-down.prevent="onArrowDown()"
                            @keydown.tab="open = false" @keydown.enter.prevent="open = false; focusButton()"
                            @keyup.space.prevent="open = false; focusButton()" style="display: none;">
                            <div class="py-1" role="none">
                                <a href="#" class="text-gray-700 block px-4 py-2 text-sm" x-state:on="Active"
                                    x-state:off="Not Active"
                                    :class="{ 'bg-gray-100 text-gray-900': activeIndex === 0, 'text-gray-700': !(activeIndex === 0) }"
                                    role="menuitem" tabindex="-1" id="menu-1-item-0"
                                    @mouseenter="onMouseEnter($event)" @mousemove="onMouseMove($event, 0)"
                                    @mouseleave="onMouseLeave($event)"
                                    @click="open = false; focusButton()">View</a>
                                <a href="#" class="text-gray-700 block px-4 py-2 text-sm"
                                    :class="{ 'bg-gray-100 text-gray-900': activeIndex === 1, 'text-gray-700': !(activeIndex === 1) }"
                                    role="menuitem" tabindex="-1" id="menu-1-item-1"
                                    @mouseenter="onMouseEnter($event)" @mousemove="onMouseMove($event, 1)"
                                    @mouseleave="onMouseLeave($event)"
                                    @click="open = false; focusButton()">Invoice</a>
                            </div>
                        </div>

                    </div>

                    <div class="hidden lg:col-span-2 lg:flex lg:items-center lg:justify-end lg:space-x-4">
                        <a href="#"
                            class="flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span>View Order</span>
                            <span class="sr-only">AT48441546</span>
                        </a>
                        <a href="#"
                            class="flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span>View Invoice</span>
                            <span class="sr-only">for order AT48441546</span>
                        </a>
                    </div>
                </div>

                <!-- Products -->
                <h4 class="sr-only">Items</h4>
                <ul role="list" class="divide-y divide-gray-200">

                    <li class="p-4 sm:p-6">
                        <div class="flex items-center sm:items-start">
                            <div
                                class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 sm:h-40 sm:w-40">
                                <img src="https://placehold.co/160x160"
                                    alt="Garment bag with two layers of grey and tan zipper pouches for folded shirts and pants."
                                    class="h-full w-full object-cover object-center">
                            </div>
                            <div class="ml-6 flex-1 text-sm">
                                <div class="font-medium text-gray-900 sm:flex sm:justify-between">
                                    <h5>Double Stack Clothing Bag</h5>
                                    <p class="mt-2 sm:mt-0">$40.00</p>
                                </div>
                                <p class="hidden text-gray-500 sm:mt-2 sm:block">Save space and protect your
                                    favorite clothes in this double-layer garment bag. Each compartment easily
                                    holds multiple pairs of jeans or tops, while keeping your items neatly
                                    folded throughout your trip.</p>
                            </div>
                        </div>

                        <div class="mt-6 sm:flex sm:justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-500"
                                    x-description="Heroicon name: mini/check-circle"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p class="ml-2 text-sm font-medium text-gray-500">Delivered on <time
                                        datetime="2021-01-05">January 5, 2021</time></p>
                            </div>

                            <div
                                class="mt-6 flex items-center space-x-4 divide-x divide-gray-200 border-t border-gray-200 pt-4 text-sm font-medium sm:mt-0 sm:ml-4 sm:border-none sm:pt-0">
                                <div class="flex flex-1 justify-center">
                                    <a href="#"
                                        class="whitespace-nowrap text-indigo-600 hover:text-indigo-500">View
                                        product</a>
                                </div>
                                <div class="flex flex-1 justify-center pl-4">
                                    <a href="#"
                                        class="whitespace-nowrap text-indigo-600 hover:text-indigo-500">Buy
                                        again</a>
                                </div>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>

        </div>
    </div>
</x-profile-layout>
