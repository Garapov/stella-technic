<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
