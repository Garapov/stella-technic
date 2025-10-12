<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminsOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $model;

    /**
     * Create a new message instance.
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новый заказ № ' . $this->model->id . ' на сайте "' . env('APP_URL', 'Стелла техник') . '"',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.forms.adminorder',
            with: [
                'order' => $this->model,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $files = $this->model->file ? [Attachment::fromPath(storage_path('app/public/' . $this->model->file))] : [];

        return $files;
    }
}
