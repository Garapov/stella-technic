<section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16" x-data="{
  products: [],
  isLoading: true,
  isReloading: false,
  init() {
    this.loadProducts();
  },
  loadProducts() {
    $wire.loadProducts($store.cart.list).then((products) => {
      this.products = products;
      this.isLoading = false;
      this.isReloading = false;

      console.log('products loaded', this.products);
    });
  },
  removeCartItem(id) {
    console.log('cart item removed', id);
    this.isReloading = true;
    $store.cart.removeFromCart(id);
    this.loadProducts();
  },
  increaseQuantity(id) {
    $store.cart.increase(id);
  },
  decreaseQuantity(id) {
    $store.cart.decrease(id);
  },
  getTotalPrice() {
    let total = 0;
    this.products.forEach(product => {
      total += product.price * +$store.cart.list[product.id];
    });
    return total;
  },
  getDiscountedPrice() {
    let total = 0;
    this.products.forEach(product => {
        let price = product.new_price ?? product.price;
        total += price * +$store.cart.list[product.id];
    });
    return total;
  }
}">

  <div class="mx-auto container px-4 2xl:px-0">
    <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
      <div>
        @livewire('general.breadcrumbs')
        <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Корзина</h2>
      </div>
    </div>

    <div x-show="isLoading" class="mt-6">
      <div class="animate-pulse">
        <div class="h-8 bg-gray-200 rounded w-1/4 mb-4"></div>
        <div class="flex gap-4">
          <div class="grow">
            <div class="h-32 bg-gray-200 rounded mb-4"></div>
            <div class="h-32 bg-gray-200 rounded mb-4"></div>
          </div>
          <div class="w-64 bg-gray-200 rounded mb-4"></div>
        </div>
      </div>
    </div>

    <template x-if="!isLoading">
      <div x-show="products.length > 0">
        <div class="mt-6 sm:mt-8 md:gap-6 lg:flex xl:gap-6">
          <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-5xl">
              <template x-if="isReloading">
                <div class="mt-6">
                    <div class="animate-pulse">
                        <div class="h-32 bg-gray-200 rounded mb-4"></div>
                        <div class="h-32 bg-gray-200 rounded mb-4"></div>
                    </div>
                </div>
              </template>
              <template x-if="!isReloading && products.length > 0">
                <div class="flex flex-col gap-6" x-show="!isReloading">
                    <template x-for="cart_item in products" :key="cart_item.id">
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                            <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                <a href="#" class="shrink-0">
                                    <img class="h-20 w-20" :src="`https://s3.stella-technic.ru/${cart_item.gallery[0]}`" alt="imac image" />
                                </a>
                                <a href="#" class="text-base font-medium text-gray-900 hover:underline dark:text-white" x-text="cart_item.name"></a>
                                <div class="relative flex items-center">
                                    <button type="button" class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"  @click="decreaseQuantity(cart_item.id)">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/>
                                        </svg>
                                    </button>
                                    <output class="bg-gray-50 border-x-0 border border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-12 py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" x-text="$store.cart.list[cart_item.id]"> </output>
                                    <button type="button" class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none" @click="increaseQuantity(cart_item.id)">
                                        <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between md:justify-end gap-4">
                                    <div class="text-end">
                                        <p class="text-lg font-extrabold leading-tight text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(cart_item.new_price ?? cart_item.price)"></p>
                                        <p class="text-md line-through font-extrabold leading-tight text-gray-600 dark:text-white" x-show="cart_item.new_price !== null" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(cart_item.price)"></p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <button type="button" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-white" @click.prevent="$store.favorites.toggleProduct(cart_item.id)">
                                            <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                :class="{ 'text-red-500 fill-red-500': $store.favorites.list[cart_item.id] }">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                            </svg>
                                        </button>

                                        <button type="button" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500" @click.prevent="removeCartItem(cart_item.id)">
                                            <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </template>
                </div>
              </template>
          </div>

          <div class="mx-auto mt-6 max-w-4xl lg:mt-0 flex-1">
            <div class="sticky top-10 space-y-6 lg:w-full">
                <div class="w-full space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                  <p class="text-xl font-semibold text-gray-900 dark:text-white">Стоимость</p>

                  <div class="space-y-4">
                    <div class="space-y-2">
                      <dl class="flex items-center justify-between gap-4">
                        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Общая стоимость</dt>
                        <dd class="text-base font-medium text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(getTotalPrice())"></dd>
                      </dl>
                      <dl class="flex items-center justify-between gap-4">
                        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Cтоимость со скидкой</dt>
                        <dd class="text-base font-medium text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(getDiscountedPrice())"></dd>
                      </dl>

                      <dl class="flex items-center justify-between gap-4">
                        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Скидка</dt>
                        <dd class="text-base font-medium text-green-600" x-text="`-${new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(getTotalPrice() - getDiscountedPrice())}`"></dd>
                      </dl>
                    </div>

                    <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                        <dt class="text-base font-bold text-gray-900 dark:text-white">Итого</dt>
                        <dd class="text-base font-bold text-gray-900 dark:text-white" x-text="new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(getDiscountedPrice())"></dd>
                    </dl>
                  </div>

                <a href="{{ route('client.checkout') }}" class="flex w-full items-center justify-center rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" wire:navigate>Перейти к оформлению</a>
              </div>

              <!-- <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                <form class="space-y-4">
                  <div>
                    <label for="voucher" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> У вас есть промокод? </label>
                    <input type="text" id="voucher" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" placeholder="" required />
                  </div>
                  <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800">Применить</button>
                </form>
              </div> -->
          </div>
        </div>
      </div>
    </template>

    <template x-if="!isLoading && products.length === 0">
      <div class="mt-6 text-center">
        <p class="text-lg text-gray-600">Ваша корзина пуста</p>
        <a href="{{ route('client.catalog.all') }}" class="mt-4 inline-block px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-500">
          Перейти в каталог
        </a>
      </div>
    </template>
  </div>

</section>
