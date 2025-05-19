<?php

namespace App\Filament\Resources\ProductVariantResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\ProductVariantResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\ProductVariantResource\Api\Transformers\ProductVariantTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = ProductVariantResource::class;


    /**
     * Show Product
     *
     * @param Request $request
     * @return ProductTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new ProductVariantTransformer($query);
    }
}
