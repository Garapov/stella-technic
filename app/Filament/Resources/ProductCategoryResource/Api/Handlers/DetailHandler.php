<?php

namespace App\Filament\Resources\ProductCategoryResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\ProductCategoryResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\ProductCategoryResource\Api\Transformers\ProductCategoryTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = ProductCategoryResource::class;


    /**
     * Show ProductCategory
     *
     * @param Request $request
     * @return ProductCategoryTransformer
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

        return new ProductCategoryTransformer($query);
    }
}
