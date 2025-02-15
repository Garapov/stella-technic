<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;

class ImageByIdColumn extends Column
{
    protected string $view = 'tables.columns.image-by-id-column';

    public function getImageUrl(?Model $record): ?string
    {
        dd($record);
    }
}
