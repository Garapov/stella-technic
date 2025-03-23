<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Outerweb\ImageLibrary\Models\Image;

class MainSlider extends Model
{
    /** @use HasFactory<\Database\Factories\MainSliderFactory> */
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "image",
        "button_text",
        "link",
        "background",
        "background_image",
        "show_on_main",
    ];
}
