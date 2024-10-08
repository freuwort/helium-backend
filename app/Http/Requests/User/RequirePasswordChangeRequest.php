<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RequirePasswordChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'requires_password_change' => ['required', 'bool'],
        ];
    }
}
