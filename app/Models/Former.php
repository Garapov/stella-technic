<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Former extends Model
{
    /** @use HasFactory<\Database\Factories\FormerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'thanks_text',
        'fields',
    ];

    protected $casts = [
        'fields' => 'array',
    ];
}
