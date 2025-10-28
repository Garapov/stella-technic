<section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16"  x-data="{
  products: [],
  constructs: [],
  isLoading: true,
  isReloading: false,
  userAuthenticated: @js(auth()->user() ? true : false),
  init() {
    this.loadProducts();
    this.loadConstructs();
  },
  loadProducts() {
    $wire.loadProducts($store.cart.list).then((products) => {
      this.products = products;
      this.isLoading = false;
      this.isReloading = false;
    });
  },
  loadConstructs() {
    $wire.loadConstructs($store.cart.constructor).then((constructs) => {
      this.constructs = constructs;
      this.isLoading = false;
      this.isReloading = false;
    });
  },
  getTotalPrice() {
    let total = 0;
    this.products.forEach(product => {
      total += (this.userAuthenticated & product.auth_price ? product.auth_price : product.price) * +$store.cart.list[product.id];
    });

    Object.keys(this.constructs).forEach(productId => {
      total += this.constructs[productId].price;
    });

    return total;
  },
  getDiscountedPrice() {
    let total = 0;
    this.products.forEach(product => {
        let price = product.new_price ?? (this.userAuthenticated & product.auth_price ? product.auth_price : product.price);
        total += price * +$store.cart.list[product.id];
    });
    Object.keys(this.constructs).forEach(productId => {
      total += this.constructs[productId].price;
    });
    return total;
  },
  makeOrder() {
    let newProds = this.products.map(product => {
      return {...product, quantity: $store.cart.list[product.id]};
    });
    let constructs = 
    $wire.placeOrder(newProds, this.constructs, this.getTotalPrice(), this.getDiscountedPrice());
  }
}">
  <div class="mx-auto container px-4 2xl:px-0">
    <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
      <div>
        {{ Breadcrumbs::render('checkout') }}
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
    <template x-if="!isLoading && (products.length < 1 && Object.keys(constructs).length > 1)">
        <div class="mt-6 text-center">
            <div x-text="products.length"></div>
            <div x-text="constructs.length"></div>
            <p class="text-lg text-gray-600">Ваша корзина пуста</p>
            <a href="{{ route('client.catalog.all') }}" class="mt-4 inline-block px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-500">
                Перейти в каталог
            </a>
        </div>
    </template>
    <template x-if="!isLoading && (products.length > 0 || Object.keys(constructs).length > 0)">
        <form @submit.prevent="makeOrder" class="mt-6 sm:mt-8 md:gap-6 lg:flex xl:gap-8" wire:loading.class="opacity-50" wire:loading.attr="disabled" wire:target="placeOrder, type, checkCompany">
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
                        <input type="text" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('name') border-red-500 dark:border-red-500 @enderror" placeholder="Иванов Иван Иванович" wire:model.live="name" />
                        @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>

                    <label>
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('email') text-red-700 dark:text-red-500 @enderror"> Email*: </span>
                        <input type="email" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('email') border-red-500 dark:border-red-500 @enderror" placeholder="ivanov112@gmail.com" wire:model.live="email" />
                        @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>

                    <label>
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('phone') text-red-700 dark:text-red-500 @enderror"> Телефон*: </span>
                        <input type="tel" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('phone') border-red-500 dark:border-red-500 @enderror" placeholder="8 (999) 999-99-99" wire:model.live="phone" x-mask="9 (999) 999-99-99" />
                        @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>
                    @if ($type === 'legal')
                    <label class="col-span-full">
                        <input class="hidden" id="file_input" type="file" wire:model.live="file">
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('file') text-red-700 dark:text-red-500 @enderror"> Файл с реквизитами: </span>
                        <div class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 flex items-center justify-between @error('file') border-red-500 dark:border-red-500 text-red-700 dark:text-red-500 @enderror">
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6" wire:loading.class="hidden" wire:target="file">
                            @if ($file)
                                @error('file')
                                <x-carbon-error />
                                @else
                                <x-carbon-checkmark-outline />
                                @enderror
                            @else
                                <x-carbon-upload />
                            @endif
                            </div>
                            <x-fas-spinner class="animate-spin w-6 h-6 hidden" wire:loading.class.remove="hidden" wire:target="file" />
                            @if ($file)
                            {{ $file->getClientOriginalName()}}
                            @else
                            <div class="text-md text-bold">Прикрепить файл с реквизитами.</div>
                            @endif
                        </div>
                        </div>
                        @error('file')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('inn') text-red-700 dark:text-red-500 @enderror"> ИНН*: </span>
                        <div class="flex flex-center gap-2">
                            <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('inn') border-red-500 dark:border-red-500 @enderror" placeholder="7710362760" wire:model.live="inn" />
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
                        <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('company_name') border-red-500 dark:border-red-500 @enderror" placeholder="Stella Technic" wire:model.live="company_name" />
                        @error('company_name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>

                    <label>
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('kpp') text-red-700 dark:text-red-500 @enderror"> КПП: </span>
                        <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('kpp') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model.live="kpp" />
                        @error('kpp')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>

                    <label>
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('bik') text-red-700 dark:text-red-500 @enderror"> БИК*: </span>
                        <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('bik') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model.live="bik" />
                        @error('bik')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>

                    <label>
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('correspondent_account') text-red-700 dark:text-red-500 @enderror"> Корреспондентский счет*: </span>
                        <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('correspondent_account') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model.live="correspondent_account" />
                        @error('correspondent_account')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>

                    <label>
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('bank_account') text-red-700 dark:text-red-500 @enderror"> Банковский счет*: </span>
                        <input class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('bank_account') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model.live="bank_account" />
                        @error('bank_account')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="col-span-1 md:col-span-2 lg:col-span-3">
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('yur_address') text-red-700 dark:text-red-500 @enderror"> Юр. адрес*: </span>
                        <textarea class="block w-full h-20 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('yur_address') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model.live="yur_address"></textarea>
                        @error('yur_address')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="col-span-1 md:col-span-2 lg:col-span-3">
                        <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white @error('legal_address') text-red-700 dark:text-red-500 @enderror"> Фактический адрес: </span>
                        <textarea class="block w-full h-20 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 @error('legal_address') border-red-500 dark:border-red-500 @enderror" placeholder="" wire:model.live="legal_address"></textarea>
                        @error('legal_address')
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
            @if ($payment_methods->isNotEmpty())
                <div class="mb-10">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Способ оплаты</h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    @foreach ($payment_methods as $payment_method)
                        <label class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800" wire:key="payment_methods.{{ $payment_method->id }}">
                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <input aria-describedby="payment_method{{ $payment_method->id }}" type="radio" name="payment-method" value="{{ $payment_method->id }}" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" wire:model.live="selected_payment_method" />
                            </div>

                            <div class="ms-4 text-sm">
                                <div class="font-medium leading-none text-gray-900 dark:text-white"> {{ $payment_method->name }} </div>
                                <p id="payment_method{{ $payment_method->id }}" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $payment_method->description }}</p>
                            </div>
                        </div>
                        </label>
                    @endforeach
                    </div>
                </div>
            @endif

            @if ($deliveries->isNotEmpty())
                <div class="mb-4">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Способ доставки</h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    @foreach ($deliveries as $delivery)
                        <label class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800 cursor-pointer" wire:key="deliveries.{{ $delivery->id }}">
                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <input aria-describedby="delivery{{ $delivery->id }}" type="radio" name="delivery-method" value="{{ $delivery->id }}" class="h-4 w-4 border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" wire:model.live="selected_delivery" />
                            </div>

                            <div class="ms-4 text-sm">
                                <div class="font-medium leading-none text-gray-900 dark:text-white"> {{ $delivery->name }} </div>
                                <p id="delivery{{ $delivery->id }}" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $delivery->description }}</p>
                            </div>
                        </div>
                        </label>

                    @endforeach
                    </div>
                </div>

                <div x-data="{
                    selected_delivery: $wire.$entangle('selected_delivery'),
                }">
                @foreach ($deliveries as $delivery)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800" :class="{ 'hidden': selected_delivery != {{ $delivery->id }} }" wire:key="deliveries.info.{{ $delivery->id }}" @if ($delivery->type == 'map' && $delivery->points)
                x-data="{
                    address: '{{ $delivery->points }}',
                    coordinates: [],
                    init() {
                        [address, coordinates] = this.address.split('|');
                        this.coordinates = coordinates.split(',');
                        this.initMap();
                    },
                    initMap() {
                        ymaps.ready(() => {
                            setTimeout(() => {
                                let map, point;

                                map = new ymaps.Map(
                                    document.getElementById(
                                        `delivery-map-{{ $delivery->id }}`,
                                    ),
                                    {
                                        center: this.coordinates,
                                        zoom: 13,
                                        controls: [],
                                    },
                                );
                                if (!point) {
                                    point = new ymaps.Placemark(this.coordinates);
                                    map.geoObjects.add(point);
                                }
                            }, 1000);
                        });
                    }
                }"
            
            @endif>
                        @switch($delivery->type)
                        @case('map')
                            @if ($delivery->points)
                                <div class="w-full h-64" id="delivery-map-{{ $delivery->id }}" wire:ignore></div>
                            @endif
                            @break

                        @case('text')
                            @if ($delivery->text)

                                <div class="text-sm text-gray-500 dark:text-gray-400 flex flex-col gap-2">
                                <label for="delivery_address{{ $delivery->id}}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Адрес доставки</label>
                                <textarea id="delivery_address{{ $delivery->id}}" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Введите адрес доставки" wire:model.live="delivery_address"></textarea>
                                {!! $delivery->text !!}
                                </div>

                            @endif
                            @break

                        @case('delivery_systems')
                            @if($delivery->images)
                                <label for="delivery_address{{ $delivery->id}}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Адрес доставки</label>
                                <textarea id="delivery_address{{ $delivery->id}}" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mb-4" placeholder="Введите адрес доставки" wire:model.live="delivery_address"></textarea>
                                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                                    @foreach ($delivery->images as $image)
                                        <div class="rounded-lg overflow-hidden">
                                            <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @break

                        @endswitch
                    </div>
                @endforeach
                </div>
            @endif
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

                    <div  x-data="{
                        init() {
                            this.smartCaptchaInit();
                        },
                        smartCaptchaInit() {
                            if (!window.smartCaptcha) return;

                            window.smartCaptcha.render($refs.smartCaptcha, {
                                sitekey: '{{ config('services.recaptcha.client_key') }}',
                                hl: 'ru',
                                callback: (token) => {
                                    $wire.set('captcha_token', token);
                                }
                            });
                        }
                    }" class="w-full">
                        <div
                            x-ref="smartCaptcha"
                            wire:ignore
                        >
                        </div>
                        @error('captcha_token')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input checked id="checked-checkbox" type="checkbox" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" wire:model="confirmation"
                        >
                        <label for="checked-checkbox" class="ms-2 text-sm font-medium @error('confirmation') text-red-500 @else text-gray-900 dark:text-gray-300  @enderror">Я согласен на обработку моих <a href="#" class="text-blue-600">персональных данных</a></label>
                    </div>

                    <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800">Оформить заказ</button>
                </div>
            </div>

        </form>
    </template>

  </div>
</section>
