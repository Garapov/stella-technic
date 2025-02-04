<?php

namespace App\Filament\Resources\OrderResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
			'email' => 'required|string',
			'phone' => 'required|string',
			'cart_items' => 'required',
			'user_id' => 'required|integer',
			'total_price' => 'required|numeric',
			'status' => 'required'
		];
    }
}
