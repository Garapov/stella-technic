<?php

namespace App\Filament\Resources\ProductVariantResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer',
            'name' => 'required|string',
            'sku' => 'string',
            'price' => 'required|integer',
            'new_price' => 'string',
            'slug' => 'string',
            'short_description' => 'string',
            'description' => 'string',
            'is_popular' => 'integer',
            'count' => 'integer',
            'synonims' => 'string',
            'gallery' => 'required|string',
            'links' => 'required|string',
            'auth_price' => 'integer',
            'seo' => 'string',
            'is_constructable' => 'integer',
            'constructor_type' => 'string',
            'rows' => 'string'
		];
    }
}
