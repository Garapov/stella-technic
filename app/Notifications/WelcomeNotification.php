<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WelcomeNotification extends Notification
{
    use Queueable;

    public $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {

        $message = (new MailMessage)
            ->subject('Спасибо за регистрацию')
            ->greeting('Здравствуйте, ' . $notifiable->name . '!')
            ->line('Спасибо за регистрацию на нашем сайте.')
            ->line('Ваши данные для входа:')
            ->line('Email: ' . $notifiable->email);

        if ($this->password) {
            $message->line('Пароль: ' . $this->password);
        }

        return $message
            ->action('Перейти к заказам', route('orders.index'))
            ->line('Благодарим за использов��ние нашего сервиса!');
    }
} 