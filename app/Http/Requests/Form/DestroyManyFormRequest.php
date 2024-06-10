<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:forms,id'],
        ];
    }
}
