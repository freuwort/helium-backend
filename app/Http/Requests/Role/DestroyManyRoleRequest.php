<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}
