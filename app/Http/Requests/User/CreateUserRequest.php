<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // User Model
            'model.username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'model.email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'model.phone' => ['nullable', 'string', 'max:255'],

            // User Info
            'user_info.salutation' => ['nullable', 'string', 'max:255'],
            'user_info.prefix' => ['nullable', 'string', 'max:255'],
            'user_info.firstname' => ['nullable', 'string', 'max:255'],
            'user_info.middlename' => ['nullable', 'string', 'max:255'],
            'user_info.lastname' => ['nullable', 'string', 'max:255'],
            'user_info.suffix' => ['nullable', 'string', 'max:255'],
            'user_info.legalname' => ['nullable', 'string', 'max:255'],
            'user_info.nickname' => ['nullable', 'string', 'max:255'],
            'user_info.organisation' => ['nullable', 'string', 'max:255'],
            'user_info.department' => ['nullable', 'string', 'max:255'],
            'user_info.job_title' => ['nullable', 'string', 'max:255'],
            'user_info.customer_id' => ['nullable', 'string', 'max:255'],
            'user_info.employee_id' => ['nullable', 'string', 'max:255'],
            'user_info.member_id' => ['nullable', 'string', 'max:255'],
            'user_info.notes' => ['nullable', 'string', 'max:1023'],

            // Main Address
            'user_info.main_address.address_line_1' => ['nullable', 'string', 'max:255'],
            'user_info.main_address.address_line_2' => ['nullable', 'string', 'max:255'],
            'user_info.main_address.city' => ['nullable', 'string', 'max:255'],
            'user_info.main_address.state' => ['nullable', 'string', 'max:255'],
            'user_info.main_address.postal_code' => ['nullable', 'string', 'max:255'],
            'user_info.main_address.country_code' => ['nullable', 'exists:countries,code'],
            'user_info.main_address.latitude' => ['nullable', 'numeric'],
            'user_info.main_address.longitude' => ['nullable', 'numeric'],
            'user_info.main_address.notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
