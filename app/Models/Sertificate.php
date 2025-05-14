<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertificate extends Model
{
    /** @use HasFactory<\Database\Factories\SertificateFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'show_on_main'
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
