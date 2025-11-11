<x-mail::message>
# Детали заказа № {{ $order->id }}

# Товары
<x-mail::table>
| Картинка      | Название      | Кол-во        | Цена          | Стоимость          | Ссылка        |
| ------------- | :-----------: | ------------: | ------------: | -----------------: | ------------: |
@foreach ($order->cart_items as $item)
    @php
        $product = \App\Models\ProductVariant::where('id', $item['id'])->first();
    @endphp
| <div style="padding: 5px; font-eweight: bold; white-space: nowrap;"><img src="{{ Storage::disk(config('filesystems.default'))->url($item['gallery'][0]) }}" style="display: block;width: 90px;"></div>      | <div style="padding: 5px; font-eweight: bold; white-space: nowrap;"><strong>{{ $item['name'] }}  (арт. {{ $item['sku'] }}) </strong></div>      | <div style="padding: 5px; font-eweight: bold; white-space: nowrap;">{{ $item['quantity'] }}</div>        | <div style="padding: 5px; font-eweight: bold; white-space: nowrap;">{{ Number::format($item['price'], locale: 'ru') . ' ₽' }}</div>          | <div style="padding: 5px; font-eweight: bold; white-space: nowrap;">{{ Number::format($item['quantity'] * $item['price'], locale: 'ru') . ' ₽' }}</div>          | @if ($product) <div style="padding: 5px; font-eweight: bold; white-space: nowrap;"><a href="{{route('client.catalog', $product->urlChain())}}"> Ссылка        </a></div> @endif |
@endforeach
|               |               |                |              |                    | <h2 style="padding: 5px; font-eweight: bold; white-space: nowrap;">Итого: {{ Number::format($order->total_price, locale: 'ru') . ' ₽' }}</h2> |
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
