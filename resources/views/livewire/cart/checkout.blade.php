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
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Оформление заказа</h2>
  
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
        
                  <button class="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Оформить заказ</button>
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