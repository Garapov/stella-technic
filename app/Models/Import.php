<?php

namespace App\Models;

use Filament\Actions\Imports\Models\Import as BaseImport;

class Import extends BaseImport
{
    protected $table = 'imports';

    protected $fillable = [
        'file_path',
        'file_name',
        'disk',
        'importer',
        'options',
        'status',
        'processed_rows',
        'total_rows',
        'successful_rows',
        'failed_rows',
        'error',
        'user_id',
    ];
}
