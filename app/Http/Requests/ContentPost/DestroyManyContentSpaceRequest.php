<?php

namespace App\Http\Requests\ContentPost;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyContentPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:content_post_groups,id'],
        ];
    }
}
