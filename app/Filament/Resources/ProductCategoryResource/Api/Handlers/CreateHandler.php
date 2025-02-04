<?php
namespace App\Filament\Resources\ProductCategoryResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ProductCategoryResource;
use App\Filament\Resources\ProductCategoryResource\Api\Requests\CreateProductCategoryRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = ProductCategoryResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create ProductCategory
     *
     * @param CreateProductCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateProductCategoryRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}