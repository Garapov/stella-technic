<section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16" x-data="{
  products: @entangle('products'),
  isLoading: true,
  init() {
    this.loadProducts();
  },
  loadProducts() {
    $wire.loadProducts($store.cart.list.filter(product => product !== null )).then((products) => {
      this.isLoading = false;
      this.products = products;
    });
  }
}">
  <div class="mx-auto container px-4 2xl:px-0">
    <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
      <div>
        @livewire('general.breadcrumbs')
        <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Оформление заказа</h2>
      </div>
    </div>
    <div x-show="isLoading" class="mt-6">
      <div class="animate-pulse">
        <div class="h-8 bg-gray-200 rounded w-1/4 mb-4"></div>
        <div class="h-32 bg-gray-200 rounded mb-4"></div>
        <div class="h-32 bg-gray-200 rounded mb-4"></div>
      </div>
    </div>


    <template x-if="!isLoading">
      <form wire:submit.prevent="placeOrder" class="mt-6 sm:mt-8 md:gap-6 lg:flex xl:gap-8">
        <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-5xl">
          <div class="mb-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
              <label class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start">
                  <div class="flex h-5 items-center">
                      <input aria-describedby="pay-on-delivery-text" type="radio" value="natural" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" wire:model.live="user_type" />
                  </div>
  
                  <div class="ms-4 text-sm">
                      <div class="font-medium leading-none text-gray-900 dark:text-white"> Для физических лиц </div>
                      <p id="pay-on-delivery-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">Оформляйте заказ как физическое лицо</p>
                  </div>
                </div>
              </label>

              <label class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start">
                  <div class="flex h-5 items-center">
                      <input aria-describedby="pay-on-delivery-text" type="radio" value="legal" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" wire:model.live="user_type" />
                  </div>
  
                  <div class="ms-4 text-sm">
                      <div class="font-medium leading-none text-gray-900 dark:text-white"> Для юридических лиц </div>
                      <p id="pay-on-delivery-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">Оформляйте заказ как юридическое лицо</p>
                  </div>
                </div>
              </label>
            </div>
            {{ $user_type }}
          </div>
          <div class="mb-10">
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <label>
                    <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> ФИО получателя*: </span>
                    <input type="text" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" placeholder="Иванов Иван Иванович" wire:model="name" />
                </label>

                <label>
                    <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Email*: </span>
                    <input type="email" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" placeholder="ivanov112@gmail.com" wire:model="email" />
                </label>

                <label>
                    <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Телефон*: </span>
                    <input type="tel" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" placeholder="8 (999) 999-99-99" wire:model="phone" x-mask="9 (999) 999-99-99" />
                </label>
              </div>
          </div>
      
          <div class="mb-10">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Способ оплаты</h3>
  
              <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <label class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex items-start">
                    <div class="flex h-5 items-center">
                        <input aria-describedby="pay-on-delivery-text" type="radio" name="payment-method" value="" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
                    </div>
    
                    <div class="ms-4 text-sm">
                        <div class="font-medium leading-none text-gray-900 dark:text-white"> Payment on delivery </div>
                        <p id="pay-on-delivery-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">+$15 payment processing fee</p>
                    </div>
                  </div>
                </label>
    
                <label class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex items-start">
                    <div class="flex h-5 items-center">
                        <input aria-describedby="pay-on-delivery-text" type="radio" name="payment-method" value="" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
                    </div>
    
                    <div class="ms-4 text-sm">
                        <div class="font-medium leading-none text-gray-900 dark:text-white">Payment on delivery</div>
                        <p id="pay-on-delivery-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">+$15 payment processing fee</p>
                    </div>
                  </div>
                </label>
    
                <label class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex items-start">
                    <div class="flex h-5 items-center">
                        <input aria-describedby="pay-on-delivery-text" type="radio" name="payment-method" value="" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
                    </div>
    
                    <div class="ms-4 text-sm">
                        <div class="font-medium leading-none text-gray-900 dark:text-white"> Payment on delivery </div>
                        <p id="pay-on-delivery-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">+$15 payment processing fee</p>
                    </div>
                  </div>
                </label>
              </div>
          </div>
      
          <div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Способ доставки</h3>
  
              <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
              <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex items-start">
                  <div class="flex h-5 items-center">
                      <input id="dhl" aria-describedby="dhl-text" type="radio" name="delivery-method" value="" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" checked />
                  </div>
  
                  <div class="ms-4 text-sm">
                      <label for="dhl" class="font-medium leading-none text-gray-900 dark:text-white"> $15 - DHL Fast Delivery </label>
                      <p id="dhl-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">Get it by Tommorow</p>
                  </div>
                  </div>
              </div>
  
              <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex items-start">
                  <div class="flex h-5 items-center">
                      <input id="fedex" aria-describedby="fedex-text" type="radio" name="delivery-method" value="" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
                  </div>
  
                  <div class="ms-4 text-sm">
                      <label for="fedex" class="font-medium leading-none text-gray-900 dark:text-white"> Free Delivery - FedEx </label>
                      <p id="fedex-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">Get it by Friday, 13 Dec 2023</p>
                  </div>
                  </div>
              </div>
  
              <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex items-start">
                  <div class="flex h-5 items-center">
                      <input id="express" aria-describedby="express-text" type="radio" name="delivery-method" value="" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
                  </div>
  
                  <div class="ms-4 text-sm">
                      <label for="express" class="font-medium leading-none text-gray-900 dark:text-white"> $49 - Express Delivery </label>
                      <p id="express-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">Get it today</p>
                  </div>
                  </div>
              </div>
              </div>
          </div>
      
        </div>

        <div class="mx-auto mt-6 max-w-4xl lg:mt-0 flex-1">
          <div class="sticky top-10 space-y-6 lg:w-full">
            <div class="w-full space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                <p class="text-xl font-semibold text-gray-900 dark:text-white">Стоимость</p>
    
                <div class="space-y-4">
                  <div class="space-y-2">
                    <dl class="flex items-center justify-between gap-4">
                      <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Общая стоимость</dt>
                      <dd class="text-base font-medium text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format($store.cart.getTotalPrice())"></dd>
                    </dl>
                    <dl class="flex items-center justify-between gap-4">
                      <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Cтоимость со скидкой</dt>
                      <dd class="text-base font-medium text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format($store.cart.getDiscountedPrice())"></dd>
                    </dl>
    
                    <dl class="flex items-center justify-between gap-4">
                      <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Скидка</dt>
                      <dd class="text-base font-medium text-green-600" x-text="`-${new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format($store.cart.getTotalPrice() - $store.cart.getDiscountedPrice())}`"></dd>
                    </dl>
    
                
                </div>
    
                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                  <dt class="text-base font-bold text-gray-900 dark:text-white">Итого</dt>
                  <dd class="text-base font-bold text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format($store.cart.getDiscountedPrice())"></dd>
                </dl>
              </div>
    
              <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" wire:navigate>Оформить заказ</button>
            </div>
  
        </div>
      </form>
    </template>

    <template x-if="!isLoading && products.length === 0">
      <div class="mt-6 text-center">
        <p class="text-lg text-gray-600">Ваша корзина пуста</p>
        <a href="{{ route('client.catalog.all') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Перейти в каталог
        </a>
      </div>
    </template>

  </div>
</section>