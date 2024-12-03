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
        <div x-show="products.length > 0" class="mt-6 sm:mt-8 md:gap-6 lg:flex xl:gap-8">
          <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-5xl">
            <div x-data="{ 
                cartItems: products,
                totalPrice: 0,
                calculateTotal() {
                    this.totalPrice = this.cartItems.reduce((total, product) => {
                        const price = product.new_price || product.price;
                        return total + (price * product.quantity);
                    }, 0);
                }
              }" 
              x-init="calculateTotal(); $watch('cartItems', () => calculateTotal())">
              <form wire:submit.prevent="placeOrder" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Имя*</label>
                    <input 
                      type="text" 
                      wire:model="name" 
                      id="name" 
                      class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                      placeholder="Введите ваше имя" 
                      required 
                    />
                    @error('name') 
                      <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                    @enderror
                  </div>

                  <div>
                    <label for="phone" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Телефон*</label>
                    <input 
                      type="tel" 
                      wire:model="phone" 
                      id="phone" 
                      class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                      placeholder="+7 (___) ___-__-__" 
                      required 
                    />
                    @error('phone') 
                      <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                    @enderror
                  </div>

                  <div class="sm:col-span-2">
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Email*</label>
                    <input 
                      type="email" 
                      wire:model="email" 
                      id="email" 
                      class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                      placeholder="email@example.com" 
                      required 
                    />
                    @error('email') 
                      <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                    @enderror
                  </div>
                </div>

                <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                  <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Ваш заказ</h3>
                  
                  <div class="space-y-2">
                    <template x-for="product in cartItems" :key="product.id">
                      <div class="flex items-center justify-between border-b pb-2">
                        <div>
                          <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="product.name"></p>
                          <p class="text-xs text-gray-500" x-text="`${product.quantity} x ${new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(product.new_price || product.price)}`"></p>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format((product.new_price || product.price) * product.quantity)"></p>
                      </div>
                    </template>
                    
                    <div class="flex justify-between pt-2">
                      <span class="text-base font-bold text-gray-900 dark:text-white">Итого</span>
                      <span class="text-base font-bold text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format($store.cart.getDiscountedPrice())"></span>
                    </div>
                  </div>
                </div>

                <button 
                  type="submit" 
                  class="w-full rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                  Оформить заказ
                </button>
              </form>
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