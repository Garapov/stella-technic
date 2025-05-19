<?php
namespace App\Filament\Resources\ProductVariantResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\ProductVariantResource;
use Illuminate\Routing\Router;


class ProductVariantApiService extends ApiService
{
    protected static string | null $resource = ProductVariantResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
