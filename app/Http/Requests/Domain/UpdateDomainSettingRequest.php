<?php

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'nullable|string|max:255',
            'company_legalname' => 'nullable|string|max:255',
            'company_slogan' => 'nullable|string|max:255',
            'company_logo' => 'nullable|string|max:255',
            'company_favicon' => 'nullable|string|max:255',
            'default_currency' => 'nullable|string|in:EUR,USD,GBP',
            'default_unit_length' => 'nullable|string|in:m,ft',
            'default_unit_weight' => 'nullable|string|in:kg,lb',
            'default_unit_volume' => 'nullable|string|in:l,gal',
            'default_unit_temperature' => 'nullable|string|in:c,f',
            'default_unit_speed' => 'nullable|string|in:kmh,mph',
        ];
    }
}
