<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateUserRequest extends FormRequest
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
            // User Model
            'model.name' => ['nullable', 'string', 'max:255'],
            'model.username' => ['nullable', 'string', 'max:255', 'unique:users,username,'.$this->user->id],
            'model.ident_number' => ['nullable', 'string', 'max:255'],
            'model.email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
            'model.email_verified_at' => ['nullable', 'date'],
            'model.enabled_at' => ['nullable', 'date'],
            
            // User Password
            'password' => ['nullable', 'string', 'min:8'],

            // User Name Model
            'user_name.salutation' => ['nullable', 'string', 'max:255'],
            'user_name.prefix' => ['nullable', 'string', 'max:255'],
            'user_name.firstname' => ['nullable', 'string', 'max:255'],
            'user_name.middlename' => ['nullable', 'string', 'max:255'],
            'user_name.lastname' => ['nullable', 'string', 'max:255'],
            'user_name.suffix' => ['nullable', 'string', 'max:255'],
            'user_name.legalname' => ['nullable', 'string', 'max:255'],
            'user_name.nickname' => ['nullable', 'string', 'max:255'],

            // User Company Model
            'user_company.company' => ['nullable', 'string', 'max:255'],
            'user_company.department' => ['nullable', 'string', 'max:255'],
            'user_company.title' => ['nullable', 'string', 'max:255'],

            // User Address Model
            'addresses' => ['nullable', 'array'],
            'addresses.*.type' => ['required', 'string', 'max:255'],
            'addresses.*.address_line_1' => ['nullable', 'string', 'max:255'],
            'addresses.*.address_line_2' => ['nullable', 'string', 'max:255'],
            'addresses.*.city' => ['nullable', 'string', 'max:255'],
            'addresses.*.state' => ['nullable', 'string', 'max:255'],
            'addresses.*.postal_code' => ['nullable', 'string', 'max:255'],
            'addresses.*.country' => ['nullable', 'string', 'max:255'],
            'addresses.*.latitude' => ['nullable', 'numeric'],
            'addresses.*.longitude' => ['nullable', 'numeric'],
            'addresses.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
