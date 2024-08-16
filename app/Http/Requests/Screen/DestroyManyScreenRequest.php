<?php

namespace App\Http\Requests\Screen;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyScreenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:screens,id'],
        ];
    }
}
