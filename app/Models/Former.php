<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Former extends Model
{
    /** @use HasFactory<\Database\Factories\FormerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'thanks_text',
        'fields',
        'button_text',
        'recipients',
        'captcha'
    ];

    protected $casts = [
        'fields' => 'array',
        'recipients' => 'array',
    ];


    public function results(): HasMany
    {
        return $this->hasMany(FormResult::class);
    }
}
