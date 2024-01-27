<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:31'],
            'icon' => ['nullable', 'string', 'max:63'],
            'permissions' => ['nullable', 'array',],
            'permissions.*' => ['required', 'distinct', 'exists:permissions,name'],
        ];
    }
}
