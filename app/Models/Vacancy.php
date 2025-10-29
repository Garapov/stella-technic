<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    /** @use HasFactory<\Database\Factories\VacancyFactory> */
    use HasFactory, Cachable;

    protected $fillable = [
        'title',
        'description',
        'badges',
    ];

    protected $casts = [
        'badges' => 'array',
    ];
}
