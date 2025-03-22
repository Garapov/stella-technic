<section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
    <div class="container mx-auto px-4 py-8">
        @if($order)
            <div class="max-w-4xl w-full mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Заказ принят
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">
                        Номер вашего заказа: <span class="font-semibold text-blue-600">{{ $order->id }}</span>
                    </p>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Детали заказа
                    </h2>

                    <div class="space-y-4">
                        @foreach($orderItems as $item)
                            <div class="flex justify-between items-center border-b pb-4 last:border-b-0">
                                <div class="flex items-center space-x-4">
                                    <img
                                        src="{{ asset('/storage/' . $item['img']['uuid'] .  '/filament-thumbnail.' . $item['img']['file_extension']) }}"
                                        alt="{{ $item['name'] }}"
                                        class="w-16 h-16 object-cover rounded"
                                    >
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $item['name'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ $item['quantity'] }} × {{ number_format($item['new_price'] ?? $item['price'], 0, ',', ' ') }} ₽
                                        </p>
                                    </div>
                                </div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format(($item['new_price'] ?? $item['price']) * $item['quantity'], 0, ',', ' ') }} ₽
                                </p>
                            </div>
                        @endforeach

                        <div class="flex justify-between items-center pt-4">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">
                                Итого
                            </p>
                            <p class="text-xl font-bold text-blue-600">
                                {{ number_format($order->total_price, 0, ',', ' ') }} ₽
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Мы свяжемся с вами в ближайшее время для подтверждения заказа.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a
                            href="{{ route('client.index') }}"
                            class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-500 transition-colors"
                        >
                            На главную
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center text-gray-600 dark:text-gray-300">
                Извините, информация о заказе недоступна.
            </div>
        @endif
    </div>
</section>
