<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Ваш заказ №' . $this->order->id . ' принят')
            ->greeting('Здравствуйте, ' . $notifiable->name . '!')
            ->line('Спасибо за ваш заказ. Мы начали его обработку.')
            ->line('Детали заказа:');
            
        // Добавляем каждый товар из заказа
        foreach ($this->order->cart_items as $item) {
            // dd($item);
            $message->line($item['name'] . ' - ' . $item['quantity'] . ' шт. x ' . number_format($item['price'], 2) . ' ₽ = ' . number_format($item['quantity'] * $item['price'], 2) . ' ₽');
        }

        $message->line('Общая сумма заказа: ' . number_format($this->order->total_price, 2) . ' ₽')
            ->action('Посмотреть заказ', route('orders.index'))
            ->line('Если у вас есть вопросы, пожалуйста, свяжитесь с нами.');

        return $message;
    }
} 