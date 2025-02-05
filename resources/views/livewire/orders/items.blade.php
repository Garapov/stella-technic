<!-- items.blade.php -->
<div class="w-full space-y-8 sm:px-4 lg:px-0">
    @forelse($orders as $order)
    <div class="w-full border-t border-b border-gray-200 bg-white shadow-sm sm:rounded-lg sm:border">
        <h3 class="sr-only">Заказ от <time datetime="{{ $order->created_at->format('Y-m-d') }}">{{ $order->created_at->format('d.m.Y') }}</time></h3>

        <div class="flex items-center border-b border-gray-200 p-4 sm:grid sm:grid-cols-4 sm:gap-x-6 sm:p-6">
            <dl class="grid flex-1 grid-cols-2 gap-x-6 text-sm sm:col-span-3 sm:grid-cols-3 lg:col-span-2">
                <div>
                    <dt class="font-medium text-gray-900">Номер заказа</dt>
                    <dd class="mt-1 text-gray-500">№{{ $order->id }}</dd>
                </div>
                <div class="hidden sm:block">
                    <dt class="font-medium text-gray-900">Дата заказа</dt>
                    <dd class="mt-1 text-gray-500">
                        <time datetime="{{ $order->created_at->format('Y-m-d') }}">{{ $order->created_at->format('d.m.Y') }}</time>
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">Итого</dt>
                    <dd class="mt-1 font-medium text-gray-900">₽{{ number_format($order->total_price, 0, ',', ' ') }}</dd>
                </div>
            </dl>

            <!-- Мобильное меню -->
            <div x-data="Components.menu({ open: false })" class="relative flex justify-end lg:hidden">
                <button type="button"
                    class="-m-2 flex items-center p-2 text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Опции для заказа №{{ $order->id }}</span>
                    <!-- ... оставить существующую SVG иконку ... -->
                </button>

                <div x-show="open" class="absolute right-0 z-10 mt-2 w-40 origin-bottom-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div class="py-1">
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm">Просмотр</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm">Накладная</a>
                    </div>
                </div>
            </div>

            <!-- Десктопные кнопки -->
            <div class="hidden lg:col-span-2 lg:flex lg:items-center lg:justify-end lg:space-x-4">
                <a href="#" class="flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span>Просмотр заказа</span>
                    <span class="sr-only">№{{ $order->id }}</span>
                </a>
                <a href="#" class="flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span>Накладная</span>
                    <span class="sr-only">для заказа №{{ $order->id }}</span>
                </a>
            </div>
        </div>

        <!-- Товары -->
        <h4 class="sr-only">Товары</h4>
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($order->cart_items as $item)
            <li class="p-4 sm:p-6">
                <div class="flex items-center sm:items-start">
                    <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 sm:h-40 sm:w-40">

                        <img class="mx-auto h-full w-full"
                            src="{{ asset('/storage/' . $item['img']['uuid'] .  '/filament-thumbnail.' . $item['img']['file_extension']) }}" alt="" />
                    </div>
                    <div class="ml-6 flex-1 text-sm">
                        <div class="font-medium text-gray-900 sm:flex sm:justify-between">
                            <h5>{{ $item['name'] }}</h5>
                            <p class="mt-2 sm:mt-0">₽{{ number_format($item['price'], 0, ',', ' ') }}</p>
                        </div>
                        <p class="hidden text-gray-500 sm:mt-2 sm:block">
                            Количество: {{ $item['quantity'] }}
                        </p>
                    </div>
                </div>

                
            </li>
            @endforeach
            <li class="p-4 sm:p-6">
                <div class="sm:flex sm:justify-between">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="ml-2 text-sm font-medium text-gray-500">
                            Статус: {{ $this->translateStatus($order->status) }}
                        </p>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    @empty
    <div class="text-center py-12">
        <p class="text-gray-500">Заказов не найдено</p>
    </div>
    @endforelse
</div>
