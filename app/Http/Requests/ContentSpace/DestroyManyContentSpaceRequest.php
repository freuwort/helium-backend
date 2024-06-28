<?php

namespace App\Http\Requests\ContentSpace;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyContentSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:content_spaces,id'],
        ];
    }
}
