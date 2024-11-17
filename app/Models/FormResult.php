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
        'former_id'
    ];

    protected $casts = [
        'results' => 'array'
    ];

    public static function boot(): void
    {
        parent::boot();

        static::created(function (Model $model) {
            Mail::to(env('MAIL_ADMIN_ADDRESS', 'ruslangarapov@yandex.ru'))->send(new FormSened($model));
        });
    }
}
