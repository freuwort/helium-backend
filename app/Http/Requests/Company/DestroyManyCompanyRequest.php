<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:companies,id'],
        ];
    }
}
