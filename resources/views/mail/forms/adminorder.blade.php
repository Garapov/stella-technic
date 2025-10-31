<x-mail::message>
# Детали заказа № {{ $order->id }}

# Товары
<x-mail::table>
| Картинка      | Название      | Кол-во        | Цена          | Итог          | Ссылка        |
| ------------- | :-----------: | ------------: | ------------: | ------------: | ------------: |
@foreach ($order->cart_items as $item)
    @php
        $product = \App\Models\ProductVariant::where('id', $item['id'])->first();
    @endphp
| <img src="{{ Storage::disk(config('filesystems.default'))->url($item['gallery'][0]) }}" style="width: 90px;">      | **{{ $item['name'] }}  (арт.{{ $item['sku'] }})**      | {{ $item['quantity'] }}        | {{ number_format($item['price'], 2) . ' ₽' }}          | {{ number_format($item['quantity'] * $item['price'], 2) . ' ₽' }}          | @if ($product) <a href="{{route('client.catalog', $product->urlChain())}}"> Ссылка        </a> @endif |
@endforeach
</x-mail::table>

@if ($order->user)
# Информация о заказчике

**Имя:** {{ $order->user['name'] }} <br>
**Email:** {{ $order->user['email'] }} <br>
**Телефон:** {{ $order->phone }} <br>
@if (isset($order->user['inn']))
**ИНН:** {{ $order->user['inn'] }} <br>
@endif
@if (isset($order->user['kpp']))
**КПП:** {{ $order->user['kpp'] }} <br>
@endif
@if (isset($order->user['bik']))
**БИК:** {{ $order->user['bik'] }} <br>
@endif
@if (isset($order->user['correspondent_account']))
**Кор. счет:** {{ $order->user['correspondent_account'] }} <br>
@endif
@if (isset($order->user['bank_account']))
**Банк. счет:** {{ $order->user['bank_account'] }} <br>
@endif
@if (isset($order->user['yur_address']))
**Юр. адрес:** {{ $order->user['yur_address'] }} <br>
@endif
@endif

@if ($order->message)
# Примечания к заказу
{{ $order->message }} <br>
@endif

# Информация о доставке
**Название:** {{ $order->delivery['name'] }} <br>
@if ($order->shipping_address)
**Адрес доставки:** {{ $order->shipping_address }} <br>
@endif

# Информация об оплате
**Название:** {{ $order->payment['name'] }} <br>

</x-mail::message>
