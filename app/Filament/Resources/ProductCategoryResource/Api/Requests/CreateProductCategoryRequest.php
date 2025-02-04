<?php

namespace App\Filament\Resources\ProductCategoryResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductCategoryRequest extends FormRequest
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
			'title' => 'required|string',
			'parent_id' => 'required|integer',
			'order' => 'required|integer',
			'icon' => 'required|string',
			'slug' => 'required|string',
			'description' => 'required|string',
			'is_visible' => 'required|integer'
		];
    }
}
