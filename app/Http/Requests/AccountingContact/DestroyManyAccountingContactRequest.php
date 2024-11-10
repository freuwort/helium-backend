<?php

namespace App\Http\Requests\AccountingContact;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyAccountingContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:accounting_contacts,id'],
        ];
    }
}
