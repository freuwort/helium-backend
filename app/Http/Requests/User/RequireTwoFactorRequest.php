<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RequireTwoFactorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'requires_two_factor' => ['required', 'bool'],
        ];
    }
}
