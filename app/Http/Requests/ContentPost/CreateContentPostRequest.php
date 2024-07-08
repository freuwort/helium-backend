<?php

namespace App\Http\Requests\ContentPost;

use Illuminate\Foundation\Http\FormRequest;

class CreateContentPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Model
            'model.space_id' => ['nullable', 'exists:content_spaces,id'],

            // Draft
            'draft.name' => ['required', 'string', 'max:255'],
            'draft.slug' => ['required', 'string', 'max:255'],
            'draft.excerpt' => ['nullable', 'string'],
            'draft.content' => ['nullable', 'string'],
            'draft.review_ready' => ['nullable', 'boolean'],
        ];
    }
}
