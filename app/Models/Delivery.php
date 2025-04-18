<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'is_active',
        'points',
        'settings',
        'description',
        'text',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];
}
