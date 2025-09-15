<div class="fixed bottom-0 left-0 right-0 z-40 block lg:hidden">
    <div class="bg-slate-50 shadow-lg rounded-t-xl">
        <div class="flex items-end gap-2 pt-4 px-4">
            
            <div class="flex-1 group">
                <a href="{{ route('client.index') }}" wire:navigate class="flex items-center flex-col gap-2 text-center w-full group-hover:text-blue-500  @if(request()->routeIs('client.index')) text-blue-500 @else text-gray-400 @endif">
                    <x-heroicon-s-home class="w-5 h-5" />
                    <span class="block text-xs">Главная</span>
                    <span class="block w-full mx-auto h-1 group-hover:bg-blue-500 rounded-t-lg @if(request()->routeIs('client.index')) bg-blue-500 @endif"></span>
                </a>
            </div>
            <div class="flex-1 group">
                <a href="{{ route('client.search') }}" wire:navigate class="flex items-center flex-col gap-2 text-center w-full group-hover:text-blue-500  @if(request()->routeIs('client.search')) text-blue-500 @else text-gray-400 @endif">
                    <x-eva-search-outline class="w-5 h-5" />
                    <span class="block text-xs">Поиск</span>
                    <span class="block w-full mx-auto h-1 group-hover:bg-blue-500 rounded-t-lg @if(request()->routeIs('client.search')) bg-blue-500 @endif"></span>
                </a>
            </div>
            <div class="flex-1 group">
                <a href="{{ route('client.cart') }}" wire:navigate class="flex items-center flex-col gap-2 text-center w-full group-hover:text-blue-500  @if(request()->routeIs('client.cart') || request()->routeIs('client.checkout')) text-blue-500 @else text-gray-400 @endif">
                    <div class="relative">
                        <x-fas-cart-arrow-down class="w-5 h-5" />
                        <div class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-gray-200 rounded-full -top-2 -end-4 dark:border-gray-700"
                        x-text="$store.cart.cartCount()"></div>
                    </div>
                    <span class="block text-xs">Корзина</span>
                    <span class="block w-full mx-auto h-1 group-hover:bg-blue-500 rounded-t-lg @if(request()->routeIs('client.cart') || request()->routeIs('client.checkout')) bg-blue-500 @endif"></span>
                </a>
            </div>
            <div class="flex-1 group">
                <a href="{{ route('client.favorites') }}" wire:navigate class="flex items-center flex-col gap-2 text-center w-full group-hover:text-blue-500  @if(request()->routeIs('client.favorites')) text-blue-500 @else text-gray-400 @endif">
                    <div class="relative">
                        <x-fas-heart class="w-5 h-5" />
                        <div class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-gray-200 rounded-full -top-2 -end-4 dark:border-gray-700"
                        x-text="$store.favorites.getCount() || '0'"></div>
                    </div>
                    <span class="block text-xs">Избранное</span>
                    <span class="block w-full mx-auto h-1 group-hover:bg-blue-500 rounded-t-lg @if(request()->routeIs('client.favorites')) bg-blue-500 @endif"></span>
                </a>
            </div>
            @auth
                <div class="flex-1 group">
                    <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center flex-col gap-2 text-center w-full group-hover:text-blue-500  @if(request()->routeIs('profile.edit')) text-blue-500 @else text-gray-400 @endif">
                        <x-fas-user class="w-5 h-5" />
                        <span class="block text-xs">Кабинет</span>
                        <span class="block w-full mx-auto h-1 group-hover:bg-blue-500 rounded-t-lg @if(request()->routeIs('profile.edit')) bg-blue-500 @endif"></span>
                    </a>
                </div>
            @endauth

            @guest
                <div class="flex-1 group">
                    <a href="{{ route('login') }}" wire:navigate class="flex items-center flex-col gap-2 text-center w-full group-hover:text-blue-500  @if(request()->routeIs('login')) text-blue-500 @else text-gray-400 @endif">
                        <x-fas-user class="w-5 h-5" />
                        <span class="block text-xs">Войти</span>
                        <span class="block w-full mx-auto h-1 group-hover:bg-blue-500 rounded-t-lg @if(request()->routeIs('login')) bg-blue-500 @endif"></span>
                    </a>
                </div>
            @endguest
        </div>
    </div>
</div>