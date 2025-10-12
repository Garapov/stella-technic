<?php

namespace App\Models;

use App\Mail\FormSened;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class FormResult extends Model
{
    /** @use HasFactory<\Database\Factories\FormResultFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'results',
        'former_id',
        'recipients'
    ];

    protected $casts = [
        'results' => 'array',
        'recipients' => 'array'
    ];

    public static function boot(): void
    {
        parent::boot();

        static::created(function (Model $model) {
            $recipients = $model->recipients == "" ? [] : explode(',', $model->recipients);
            Mail::to(env('MAIL_ADMIN_ADDRESS', 'ruslangarapov@yandex.ru'))
            ->cc($recipients)->queue((new FormSened($model))->onQueue('mails'));
        });
    }
}
