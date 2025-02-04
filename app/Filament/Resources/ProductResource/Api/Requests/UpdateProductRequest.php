<?php

namespace App\Filament\Resources\ProductResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
			'name' => 'required|string',
			'image' => 'required|string',
			'slug' => 'required|string',
			'gallery' => 'required|string',
			'short_description' => 'required|string',
			'description' => 'required|string',
			'price' => 'required|integer',
			'new_price' => 'required|integer',
			'is_popular' => 'required|integer',
			'count' => 'required|integer',
			'synonims' => 'required|string',
			'deleted_at' => 'required'
		];
    }
}
