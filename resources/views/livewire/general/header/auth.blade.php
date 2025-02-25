@auth
    <div class="relative" x-data="dropdown" @click.outside="close">
        <button type="button" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800 inline-flex items-center" @click="toggle">
            <x-fas-user class="w-4 h-4 me-3" />                        
            {{ auth()->user()->name }}
            <x-fas-arrow-down class="w-3 h-3 ms-2.5" x-show="!isOpened"/>
            <x-fas-arrow-up class="w-3 h-3 ms-2.5" x-show="isOpened"/>
        </button>
        <div class="absolute top-[calc(100%+10px)] right-0 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600" x-show="isOpened">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDividerButton">
                <li>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" wire:navigate>Личный кабинет</a>
                </li>
                <li>
                    <a href="{{ route('filament.admin.resources.users.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Админка</a>
                </li>
            </ul>
            <div class="py-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <a href="route('logout')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
@endauth

@guest
    <a href="{{ route('login') }}"
        class="relative inline-flex items-center p-3 text-sm font-medium text-center text-white bg-blue-500 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" wire:navigate>
        <x-fas-user class="w-4 h-4" />
    </a>
@endguest