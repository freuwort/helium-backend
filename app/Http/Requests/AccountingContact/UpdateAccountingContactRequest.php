<?php

namespace App\Http\Requests\AccountingContact;

use App\Rules\Address;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountingContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'vat_id' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'string', 'max:255'],
            'supplier_id' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'main_address' => ['nullable', new Address],
            'billing_address' => ['nullable', new Address],
            'shipping_address' => ['nullable', new Address],
        ];
    }
}
