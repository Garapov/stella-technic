<?php
namespace App\Filament\Resources\ProductVariantResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ProductVariantResource;
use App\Filament\Resources\ProductVariantResource\Api\Requests\CreateProductVariantRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = ProductVariantResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Product
     *
     * @param CreateProductVariantRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateProductVariantRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}