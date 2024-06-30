<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Model
            'model.parent_id' => ['nullable', 'exists:categories,id'],
            'model.inherit_access' => ['nullable', 'bool'],
            'model.type' => ['required', 'string', 'in:post,product'],
            'model.name' => ['nullable', 'string', 'max:255'],
            'model.slug' => ['nullable', 'string', 'max:255'],
            'model.content' => ['nullable', 'string'],
            'model.icon' => ['nullable', 'string', 'max:255'],
            'model.color' => ['nullable', 'string', 'max:255'],
            'model.hidden' => ['nullable', 'bool'],
        ];
    }
}
