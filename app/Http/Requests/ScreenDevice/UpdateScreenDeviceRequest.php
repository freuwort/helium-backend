<?php

namespace App\Http\Requests\ScreenDevice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScreenDeviceRequest extends FormRequest
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
            'model.group' => ['nullable', 'string', 'max:255'],

            // Address
            'address' => ['nullable', 'array'],
            'address.address_line_1' => ['nullable', 'string', 'max:255'],
            'address.address_line_2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:255'],
            'address.state' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['nullable', 'string', 'max:255'],
            'address.country_code' => ['nullable', 'exists:countries,code'],
            'address.latitude' => ['nullable', 'numeric'],
            'address.longitude' => ['nullable', 'numeric'],
            'address.notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
