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


    <template x-if="!isLoading && products.length > 0">
      <form wire:submit.prevent="placeOrder" class="mt-6 sm:mt-8 md:gap-6 lg:flex xl:gap-8" wire:loading.class="opacity-50" wire:loading.attr="disabled" wire:target="placeOrder, type, checkCompany">
        <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-5xl">
          @if ($message)
            <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-400 dark:border-yellow-800" role="alert">
              <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"></path>
              </svg>
              <span class="sr-only">Info</span>
              <div>
                  {{ $message }}
              </div>
            </div>
          @endif
          <div class="mb-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
              <label class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start">
                  <div class="flex h-5 items-center">
                      <input aria-describedby="pay-on-delivery-text" type="radio" value="natural" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" wire:model.live="type" />
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
                      <input aria-describedby="pay-on-delivery-text" type="radio" value="legal" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" wire:model.live="type" />
                  </div>
  
                  <div class="ms-4 text-sm">
                      <div class="font-medium leading-none text-gray-900 dark:text-white"> Для юридических лиц </div>
                      <p id="pay-on-delivery-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">Оформляйте заказ как юридическое лицо</p>
                  </div>
                </div>
              </label>
            </div>
          </div>
          <div class="mb-10">
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <label>
                    <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('name') text-red-700 dark:text-red-500 @enderror"> ФИО получателя*: </span>
                    <input type="text" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('name') border-red-500 dark:border-red-500 @enderror"" placeholder="Иванов Иван Иванович" wire:model="name" />
                    @error('name')
                      <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </label>

                <label>
                    <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('email') text-red-700 dark:text-red-500 @enderror"> Email*: </span>
                    <input type="email" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('email') border-red-500 dark:border-red-500 @enderror" placeholder="ivanov112@gmail.com" wire:model="email" />
                    @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </label>

                <label>
                    <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('phone') text-red-700 dark:text-red-500 @enderror"> Телефон*: </span>
                    <input type="tel" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('phone') border-red-500 dark:border-red-500 @enderror" placeholder="8 (999) 999-99-99" wire:model="phone" x-mask="9 (999) 999-99-99" />
                    @error('phone')
                      <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </label>
                @if ($type === 'legal')
                  <label class="col-span-full">
                    <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file</s-an>
                    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="file_input_help" id="file_input" type="file">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG, PNG, JPG or GIF (MAX. 800x400px).</p>
                  </label>
                  <label>
                      <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('inn') text-red-700 dark:text-red-500 @enderror"> ИНН*: </span>
                      <div class="flex flex-center gap-2">
                        <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('inn') border-red-500 dark:border-red-500 @enderror" placeholder="7710362760" wire:model="inn" />
                        <div wire:click="checkCompany" class="text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800 cursor-pointer">
                          <x-heroicon-o-arrow-small-right wire:loading.class="hidden" wire:target="checkCompany" class="w-5 h-5" />
                          <x-fas-spinner class="animate-spin w-5 h-5" wire:loading wire:target="checkCompany" />
                        </div>
                      </div>
                      @error('inn')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                      @enderror
                  </label>

                  <label>
                      <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('company_name') text-red-700 dark:text-red-500 @enderror"> Название компании*: </span>
                      <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('company_name') border-red-500 dark:border-red-500 @enderror" placeholder="Stella Technic" wire:model="company_name" />
                      @error('company_name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                      @enderror
                  </label>

                  <label>
                      <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('kpp') text-red-700 dark:text-red-500 @enderror"> КПП: </span>
                      <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('kpp') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model="kpp" />
                      @error('kpp')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                      @enderror
                  </label>

                  <label>
                      <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('bik') text-red-700 dark:text-red-500 @enderror"> БИК*: </span>
                      <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('bik') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model="bik" />
                      @error('bik')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                      @enderror
                  </label>

                  <label>
                      <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('correspondent_account') text-red-700 dark:text-red-500 @enderror"> Корреспондентский счет*: </span>
                      <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('correspondent_account') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model="correspondent_account" />
                      @error('correspondent_account')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                      @enderror
                  </label>

                  <label>
                      <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('bank_account') text-red-700 dark:text-red-500 @enderror"> Банковский счет*: </span>
                      <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('bank_account') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model="bank_account" />
                      @error('bank_account')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                      @enderror
                  </label>

                  <label class="col-span-1 md:col-span-2 lg:col-span-3">
                      <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('yur_address') text-red-700 dark:text-red-500 @enderror"> Юр. адрес*: </span>
                      <textarea class="block w-full h-20 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('yur_address') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model="yur_address"></textarea>
                      @error('yur_address')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                      @enderror
                  </label>
                @endif
              </div>
          </div>

          <div class="mb-10">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Примечания к заказу</h3>
  
              <div class="grid grid-cols-1 gap-4 md:grid-cols-1">
                <label>
                  <textarea class="block w-full h-40 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" placeholder="Укажите желаемое время и дату доставки, информацию о проезде к вашему дому" wire:model.live="comment" > </textarea>  
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
    
              <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800 flex items-center gap-2" wire:loading.class="opacity-50" wire:target="placeOrder">
                Оформить заказ
                <x-fas-spinner class="animate-spin w-3 h-3" wire:loading wire:target="placeOrder" />
              </button>
            </div>
  
        </div>
      </form>
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