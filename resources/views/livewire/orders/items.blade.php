<!-- items.blade.php -->
<div class="w-full flex flex-col gap-4 sm:px-4 lg:px-0" x-data="{
    orders: [],
    isLoading: true,
    init() {
        this.loadOrders();
    },
    loadOrders() {
        $wire.loadOrders().then(orders => {
            console.log(orders);
            this.orders = orders;
            this.isLoading = false;
        })
    }
}">
    <template x-if="isLoading && !orders.length">
        <!-- Skeleton Loader -->
        <div class="animate-pulse space-y-4">
            <div class="h-10 bg-gray-200 rounded"></div>
            <div class="h-10 bg-gray-200 rounded w-3/4"></div>
            <div class="h-10 bg-gray-200 rounded w-1/2"></div>
        </div>
    </template>

    <template x-for="order in orders" :key="order.id">
        <div class="w-full border-t border-b border-gray-200 bg-white shadow-sm sm:rounded-lg sm:border">
            <div class="flex items-center border-b border-gray-200 p-4 sm:grid sm:grid-cols-4 sm:gap-x-6 sm:p-6">
                <div class="grid flex-1 grid-cols-2 gap-x-6 text-sm sm:col-span-3 sm:grid-cols-3 lg:col-span-2">
                    <div>
                        <div class="font-medium text-gray-900">Номер заказа</div>
                        <div class="mt-1 text-gray-500">№ <span x-text="order.id"></span> </div>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Итого</div>
                        <div class="mt-1 font-medium text-gray-900">₽ <span x-text="order.total_price"></span></div>
                    </div>
                </div>

                <!-- Десктопные кнопки -->
                <div class="hidden lg:col-span-2 lg:flex lg:items-center lg:justify-end lg:space-x-4">
                    <div class="sm:flex sm:justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="ml-2 text-sm font-medium text-gray-500">
                                Статус: <span x-text="order.status"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Товары -->
            <h4 class="sr-only">Товары</h4>
            <ul role="list" class="divide-y divide-gray-200">
                <template x-for="product in order.cart_items" :key="product.id">
                    <li class="p-4 sm:p-6">
                        <div class="flex gap-4 items-center sm:items-start">
                            <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 sm:h-40 sm:w-40">

                                <img class="mx-auto h-full w-full"
                                    :src="`https://s3.stella-technic.ru/${product.gallery[0]}`" alt="" />
                            </div>
                            <div class="ml-6 flex-1 text-sm">
                                <div class="font-medium text-gray-900 sm:flex sm:justify-between">
                                    <h5 x-text="product.name"></h5>
                                    <p class="mt-2 sm:mt-0">₽<span x-text="product.price"></span></p>
                                </div>
                                <p class="hidden text-gray-500 sm:mt-2 sm:block">
                                    Количество: <span x-text="product.quantity"></span>
                                </p>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
    </template>
</div>
