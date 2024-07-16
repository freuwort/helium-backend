<?php

namespace App\Http\Requests\ContentPost;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewStatusOnContentPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Draft
            'draft.review_ready' => ['nullable', 'boolean'],
        ];
    }
}
