<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainSlider extends Model
{
    /** @use HasFactory<\Database\Factories\MainSliderFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'button_text',
        'link',
    ];
}
