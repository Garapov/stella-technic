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
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Корзина</h2>

    <div x-show="isLoading" class="mt-6">
      <div class="animate-pulse">
        <div class="h-8 bg-gray-200 rounded w-1/4 mb-4"></div>
        <div class="h-32 bg-gray-200 rounded mb-4"></div>
        <div class="h-32 bg-gray-200 rounded mb-4"></div>
      </div>
    </div>

    <template x-if="!isLoading">
      <div x-show="products.length > 0">
        <div class="mt-6 sm:mt-8 md:gap-6 lg:flex xl:gap-8">
          <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-5xl">
            <div class="space-y-6">
              <template x-for="cart_item in products" :key="cart_item.id">
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                  <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                    <a href="#" class="shrink-0 md:order-1">
                      <img class="h-20 w-20 dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg" alt="imac image" />
                      <img class="hidden h-20 w-20 dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg" alt="imac image" />
                    </a>
            
                    <div class="flex items-center justify-between md:order-3 md:justify-end">
                      {{--<div class="flex items-center">
                        <button type="button" id="decrement-button" data-input-counter-decrement="counter-input" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700" @click="$store.cart.decrease(cart_item.id); loadProducts();">
                          <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                          </svg>
                        </button>
                        <input type="number" id="counter-input" data-input-counter class="w-20 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" x-model="cart_item.quantity" min="1" @change="$store.cart.validateCount(cart_item.id); loadProducts();" />
                        <button type="button" id="increment-button" data-input-counter-increment="counter-input" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700" @click="$store.cart.increase(cart_item.id); loadProducts();">
                          <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                          </svg>
                        </button>
                      </div>
                      --}}
                      <div class="text-end md:order-4 md:w-32">
                        <p class="text-lg font-extrabold leading-tight text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(cart_item.new_price ?? cart_item.price)"></p>
                        <p class="text-md line-through font-extrabold leading-tight text-gray-600 dark:text-white" x-show="cart_item.new_price !== null" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(cart_item.price)"></p>
                      </div>
                    </div>
            
                    <div class="w-full min-w-0 flex-1 space-y-4 md:order-2">
                      <div>
                        <a href="#" class="text-base font-medium text-gray-900 hover:underline dark:text-white" x-text="cart_item.name"></a>
                        <div class="mt-2 flex flex-wrap gap-2 pb-2">
                          <template x-for="variant in cart_item.variants" :key="variant.id">
                            <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white p-2 dark:border-gray-700 dark:bg-gray-800">
                              <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="variant.name"></span>
                              </div>
                              
                              <div class="flex items-center gap-1">
                                <button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700" 
                                  @click="$store.cart.decreaseVariation(cart_item.id, variant.id); loadProducts();"
                                  x-bind:disabled="!cart_item.cart_variations || !cart_item.cart_variations[variant.id]"
                                  x-show="cart_item.cart_variations && cart_item.cart_variations[variant.id]"
                                >
                                  <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                  </svg>
                                </button>
                                
                                <div
                                  class="w-12 border-0 bg-transparent p-0 text-center text-sm text-gray-900 focus:outline-none focus:ring-0 dark:text-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                                  x-text="cart_item.cart_variations && cart_item.cart_variations[variant.id] ? cart_item.cart_variations[variant.id].count : 0"
                                  x-show="cart_item.cart_variations && cart_item.cart_variations[variant.id]"
                                ></div>
                                
                                <button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700" 
                                  @click="$store.cart.increaseVariation(cart_item.id, variant.id); loadProducts();"
                                  x-bind:disabled="!cart_item.cart_variations || !cart_item.cart_variations[variant.id]"
                                  x-show="cart_item.cart_variations && cart_item.cart_variations[variant.id]"
                                >
                                  <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                  </svg>
                                </button>
                                
                                <button 
                                  type="button" 
                                  class="ml-2 text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-700" 
                                  @click="$store.cart.removeVariation(cart_item.id, variant.id); loadProducts();"
                                  x-show="cart_item.cart_variations && cart_item.cart_variations[variant.id]"
                                >
                                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                  </svg>
                                </button>
                                
                                <button 
                                  type="button" 
                                  class="ml-2 text-green-600 hover:text-green-800 dark:text-green-500 dark:hover:text-green-700" 
                                  @click="$store.cart.addVariationToCart({
                                    product: cart_item, 
                                    variation: variant, 
                                    count: 1
                                  }); loadProducts();"
                                  x-show="!cart_item.cart_variations || !cart_item.cart_variations[variant.id]"
                                >
                                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                  </svg>
                                </button>
                              </div>
                            </div>
                          </template>
                        </div>
            
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
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
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
      
                <a href="{{ route('client.checkout') }}" class="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" wire:navigate>Перейти к оформлению</a>
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
    </template>

    <template x-if="!isLoading && products.length === 0">
      <div class="mt-6 text-center">
        <p class="text-lg text-gray-600">Ваша корзина пуста</p>
        <a href="{{ route('client.catalog', ['slug' => 'all']) }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Перейти в каталог
        </a>
      </div>
    </template>
  </div>
    
</section>