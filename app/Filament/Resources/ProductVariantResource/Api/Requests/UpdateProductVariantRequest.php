<?php

namespace App\Filament\Resources\ProductVariantResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest extends FormRequest
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
            'name' => 'string',
            'price' => 'integer',
            'new_price' => 'integer',
            'short_description' => 'string',
            'description' => 'string',
            'count' => 'integer',
            'auth_price' => 'integer'
		];
    }
}
