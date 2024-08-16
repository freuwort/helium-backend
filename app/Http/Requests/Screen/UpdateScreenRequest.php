<?php

namespace App\Http\Requests\Screen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScreenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Model
            'model.name' => ['nullable', 'string', 'max:255'],
            'model.content' => ['nullable', 'array'],
            'model.background' => ['nullable', 'string', 'max:7'],
            'model.width' => ['nullable', 'numeric', 'between:0,99999'],
            'model.height' => ['nullable', 'numeric', 'between:0,99999'],
            'model.duration' => ['nullable', 'numeric', 'between:0,999999999'],
        ];
    }
}
