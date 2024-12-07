<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Outerweb\ImageLibrary\Models\Image as ModelImage;

class Image extends ModelImage
{
    use HasFactory;
    
    protected $table = 'images';
    
}
