<x-mail::message>
# Детали заказа № {{ $order->id }}

@foreach ($order->cart_items as $item)
{{ $item['name'] . ' - **' . $item['quantity'] . ' шт. x ' . number_format($item['price'], 2) . ' ₽ = ' . number_format($item['quantity'] * $item['price'], 2) . ' ₽**' }} <br>
@endforeach
@if ($order->user)
# Информация о заказчике

**Имя:** {{ $order->user['name'] }} <br>
**Email:** {{ $order->user['email'] }} <br>
**Телефон:** {{ $order->user['phone'] }} <br>
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

# Информация о доставке
**Название:** {{ $order->delivery['name'] }} <br>

# Информация об оплате
**Название:** {{ $order->payment['name'] }} <br>

</x-mail::message>
