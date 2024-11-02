<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // User Model
            'model.username' => ['nullable', 'string', 'max:255', 'unique:users,username,'.$this->user->id],
            'model.email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
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
        ];
    }
}
