<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
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
            'model.slug' => ['nullable', 'string', 'max:255', 'unique:forms,slug,'.$this->form->id],
            'model.description' => ['nullable', 'string'],

            // Fields
            'form_fields' => ['nullable', 'array'],
            'form_fields.*.name' => ['nullable', 'string', 'max:255'],
            'form_fields.*.key' => ['required', 'string', 'max:255'],
            'form_fields.*.validation' => ['nullable', 'array'],
        ];
    }
}
