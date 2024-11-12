<?php

namespace App\Http\Requests\User;

use App\Rules\Address;
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
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:255'],

            'salutation' => ['nullable', 'string', 'max:255'],
            'prefix' => ['nullable', 'string', 'max:255'],
            'firstname' => ['nullable', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['nullable', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:255'],
            'legalname' => ['nullable', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],

            'organisation' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],

            'customer_id' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'member_id' => ['nullable', 'string', 'max:255'],

            'notes' => ['nullable', 'string', 'max:1023'],

            'main_address' => ['nullable', new Address],
            'billing_address' => ['nullable', new Address],
            'shipping_address' => ['nullable', new Address],
        ];
    }
}
