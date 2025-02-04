<?php

namespace App\Filament\Resources\UserResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
			'type' => 'required|string',
			'name' => 'required|string',
			'email' => 'required|string',
			'password' => 'required|string',
			'company_name' => 'required|string',
			'phone' => 'required|string',
			'inn' => 'required|string',
			'kpp' => 'required|string',
			'bik' => 'required|string',
			'correspondent_account' => 'required|string',
			'bank_account' => 'required|string',
			'yur_address' => 'required|string',
			'email_verified_at' => 'required',
			'remember_token' => 'required|string'
		];
    }
}
