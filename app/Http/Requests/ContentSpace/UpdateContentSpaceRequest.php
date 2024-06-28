<?php

namespace App\Http\Requests\ContentSpace;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContentSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Model
            'model.parent_id' => ['nullable', 'exists:content_spaces,id'],
            'model.name' => ['nullable', 'string', 'max:255', 'unique:content_spaces,name,'.$this->id.',id,parent_id,'.$this->model['parent_id']],
            'model.inherit_access' => ['nullable', 'bool'],
        ];
    }
}
