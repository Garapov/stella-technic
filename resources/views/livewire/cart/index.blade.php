<section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
    <div class="mx-auto container px-4 2xl:px-0">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Корзина</h2>
  
      <div class="mt-6 sm:mt-8 md:gap-6 lg:flex xl:gap-8">
        <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-5xl">
          <div class="space-y-6">
            <template x-for="cart_item in $store.cart.list.filter(product => product !== null )" :key="cart_item.id">
              <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                  <a href="#" class="shrink-0 md:order-1">
                    <img class="h-20 w-20 dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg" alt="imac image" />
                    <img class="hidden h-20 w-20 dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg" alt="imac image" />
                  </a>
            
                  <label for="counter-input" class="sr-only">Choose quantity:</label>
                  <div class="flex items-center justify-between md:order-3 md:justify-end">
                    <div class="flex items-center">
                      <button type="button" id="decrement-button" data-input-counter-decrement="counter-input" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700" @click="$store.cart.decrease(cart_item.id)">
                        <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                        </svg>
                      </button>
                      <input type="number" id="counter-input" data-input-counter class="w-20 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" x-model="cart_item.count" min="1" @change="$store.cart.validateCount(cart_item.id)" />
                      <button type="button" id="increment-button" data-input-counter-increment="counter-input" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700" @click="$store.cart.increase(cart_item.id)">
                        <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                        </svg>
                      </button>
                    </div>
                    <div class="text-end md:order-4 md:w-32">
                      <p class="text-lg font-extrabold leading-tight text-gray-900 dark:text-white"><span x-text="cart_item.new_price ?? cart_item.price"></span> р.</p>
                      <p class="text-md line-through font-extrabold leading-tight text-gray-600 dark:text-white" x-show="cart_item.new_price !== null"><span x-text="cart_item.price"></span> р.</p>
                    </div>
                  </div>
            
                  <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                    <a href="#" class="text-base font-medium text-gray-900 hover:underline dark:text-white" x-text="cart_item.name"></a>
            
                    <div class="flex items-center gap-4">
                      <button type="button" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-white">
                        <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                        </svg>
                      </button>
            
                      <button type="button" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500" @click="$store.cart.removeFromCart(cart_item.id)">
                        <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        Удалить
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
          {{-- <div class="hidden xl:mt-8 xl:block">
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">Рекомендуем к покупке</h3>
            <div class="mt-6 grid grid-cols-3 gap-4 sm:mt-8">
                @livewire('general.product')
                @livewire('general.product')
                @livewire('general.product')
            </div>
          </div> --}}
        </div>
  
        <div class="mx-auto mt-6 max-w-4xl lg:mt-0 flex-1">
          <div class="sticky top-10 space-y-6 lg:w-full">
            <div class="w-full space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                <p class="text-xl font-semibold text-gray-900 dark:text-white">Стоимость</p>
      
                <div class="space-y-4">
                  <div class="space-y-2">
                    <dl class="flex items-center justify-between gap-4">
                      <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Общая стоимость</dt>
                      <dd class="text-base font-medium text-gray-900 dark:text-white" x-text="$store.cart.getTotalPrice()"></dd>
                    </dl>
                    <dl class="flex items-center justify-between gap-4">
                      <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Cтоимость со скидкой</dt>
                      <dd class="text-base font-medium text-gray-900 dark:text-white" x-text="$store.cart.getDiscountedPrice()"></dd>
                    </dl>
      
                    <dl class="flex items-center justify-between gap-4">
                      <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Скидка</dt>
                      <dd class="text-base font-medium text-green-600" x-text="`-${new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format($store.cart.price - $store.cart.discountedPrice)}`"></dd>
                    </dl>
      
                  
                  </div>
      
                  <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                    <dt class="text-base font-bold text-gray-900 dark:text-white">Итого</dt>
                    <dd class="text-base font-bold text-gray-900 dark:text-white" x-text="$store.cart.getDiscountedPrice()"></dd>
                  </dl>
                </div>
      
                <a href="{{ route('client.checkout') }}" class="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" wire:navigate>Оформить заказ</a>
              </div>
      
              <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                <form class="space-y-4">
                  <div>
                    <label for="voucher" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> У вас есть промокод? </label>
                    <input type="text" id="voucher" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" placeholder="" required />
                  </div>
                  <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Применить</button>
                </form>
              </div>
          </div>
        </div>
      </div>
    </div>
  </section>