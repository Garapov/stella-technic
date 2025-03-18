<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900 relative flex flex-col" x-data="{
    product: @js($product),
}">
    @if($product->trashed())
        <div class="absolute inset-0 bg-black/50 rounded-lg z-10 flex items-center justify-center">
            <div class="bg-red-600 text-white px-4 py-2 rounded-md font-medium">
                Товар недоступен
            </div>
        </div>
    @endif

    <div class="grid grid-cols-4 gap-4">
        <div class="rounded-lg overflow-hidden">
            <img src="/storage/{{ $product->img->uuid }}/original.{{ $product->img->file_extension }}" alt="{{ $product->img->alt }}" />
        </div>
        <div class="col-span-3 flex flex-col gap-4">
            <h3 class="text-lg sm:text-xl font-semibold text-slate-900">{{ $product->name }}</h3>
            <div class="flex flex-col gap-2">
                {!! str($product->description)->sanitizeHtml() !!}
            </div>
        </div>
    </div>

    @php
        // Инициализируем пустой массив для уникальных параметров
        $uniqueParamNames = [];
        
        // Проходим по всем вариантам продукта
        foreach($product->variants as $variant) {
            // Проверяем, есть ли у варианта параметры
            if ($variant->paramItems && $variant->paramItems->count() > 0) {
                // Для каждого элемента параметра в варианте
                foreach($variant->paramItems as $paramItem) {
                    // Если у параметра есть имя, добавляем его в массив уникальных параметров
                    if ($paramItem->productParam && !empty($paramItem->productParam->name && $paramItem->productParam->show_on_table)) {
                        $uniqueParamNames[$paramItem->productParam->id] = $paramItem->productParam->name;
                    }
                }
            }
            if ($variant->parametrs && $variant->parametrs->count() > 0) {
                // Для каждого элемента параметра в варианте
                foreach($variant->parametrs as $parametrs) {
                    // Если у параметра есть имя, добавляем его в массив уникальных параметров
                    if ($parametrs->productParam && !empty($parametrs->productParam->name) && $parametrs->productParam->show_on_table) {
                        $uniqueParamNames[$parametrs->productParam->id] = $parametrs->productParam->name;
                    }
                }
            }
        }
    @endphp

    <div class="relative overflow-x-auto sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Артикул
                    </th>
                    @foreach($uniqueParamNames as $paramName)
                        <th scope="col" class="px-6 py-3">
                            {{ $paramName }}
                        </th>
                    @endforeach
                    <th scope="col" class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product->variants as $variant)
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800" x-data="{
                        cart_quantity: 1,
                        product: @js($product),
                        variant: @js($variant),
                        init () {
                            console.log(this.variant);
                        },
                        addVariationToCart: function () {
                            $store.cart.addVariationToCart({
                                product: this.product,
                                count: this.cart_quantity,
                                variation: this.variant
                            });
                        }
                    }">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <a href="{{ route('client.product_detail', $variant->slug) }}" wire:navigate>{{ $variant->sku }}</a>
                        </th>
                        @foreach($variant->paramItems as $paramItem)
                            @if (!$paramItem->productParam->show_on_table) 
                                @continue
                            @endif
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $paramItem->title }}
                            </th>
                        @endforeach
                        @foreach($variant->parametrs as $parametrs)
                            @if (!$parametrs->productParam->show_on_table) 
                                @continue
                            @endif
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $parametrs->title }}
                            </th>
                        @endforeach
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <div class="flex items-center justify-end">
                                <button type="button" class="inline-flex items-center rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 dark:focus:ring-blue-800" @click="addVariationToCart()">
                                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round"  stroke-linejoin="round" stroke-width="2" d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                    </svg>
                                </button>
                            </div>
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>