<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ImportUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }



    public function prepareForValidation(): void
    {
        $this->merge(['items' =>
            collect($this->items)
            ->map(fn ($item) => [
                ...$item,
                'enabled_at' => now(),
                'email_verified_at' => now(),
                'roles' => is_string($item['roles']) ? explode(',', $item['roles']) : $item['roles'],
                'user_name' => collect($item)
                    ->filter(fn ($value, $key) => str_starts_with($key, 'user_name_'))
                    ->reduce(function ($carry, $value, $key) {
                        $newKey = str_replace('user_name_', '', $key);
                        $carry[$newKey] = $value;
                        return $carry;
                    }, []),
                'user_company' => collect($item)
                    ->filter(fn ($value, $key) => str_starts_with($key, 'user_company_'))
                    ->reduce(function ($carry, $value, $key) {
                        $newKey = str_replace('user_company_', '', $key);
                        $carry[$newKey] = $value;
                        return $carry;
                    }, []),
            ])
            ->toArray(),
        ]);
    }

    
    
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            // User Model
            'items.*.username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'items.*.email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'items.*.password' => ['nullable', 'string', 'max:255'],
            'items.*.email_verified_at' => ['nullable', 'date'],
            'items.*.enabled_at' => ['nullable', 'date'],

            // User Roles
            'items.*.roles' => ['nullable', 'array'],
            'items.*.roles.*' => ['required', 'exists:roles,id'],

            // User Name
            'items.*.user_name.salutation' => ['nullable', 'string', 'max:255'],
            'items.*.user_name.prefix' => ['nullable', 'string', 'max:255'],
            'items.*.user_name.firstname' => ['nullable', 'string', 'max:255'],
            'items.*.user_name.middlename' => ['nullable', 'string', 'max:255'],
            'items.*.user_name.lastname' => ['nullable', 'string', 'max:255'],
            'items.*.user_name.suffix' => ['nullable', 'string', 'max:255'],
            'items.*.user_name.legalname' => ['nullable', 'string', 'max:255'],
            'items.*.user_name.nickname' => ['nullable', 'string', 'max:255'],

            // User Company
            'items.*.user_company.company' => ['nullable', 'string', 'max:255'],
            'items.*.user_company.department' => ['nullable', 'string', 'max:255'],
            'items.*.user_company.title' => ['nullable', 'string', 'max:255'],

            // // User Identifiers
            // 'identifiers' => ['nullable', 'array'],
            // 'identifiers.*.type' => ['nullable', 'string', 'max:255'],
            // 'identifiers.*.label' => ['nullable', 'string', 'max:255'],
            // 'identifiers.*.value' => ['nullable', 'string', 'max:255'],

            // // User Addresses
            // 'addresses' => ['nullable', 'array'],
            // 'addresses.*.type' => ['required', 'string', 'max:255'],
            // 'addresses.*.address_line_1' => ['nullable', 'string', 'max:255'],
            // 'addresses.*.address_line_2' => ['nullable', 'string', 'max:255'],
            // 'addresses.*.city' => ['nullable', 'string', 'max:255'],
            // 'addresses.*.state' => ['nullable', 'string', 'max:255'],
            // 'addresses.*.postal_code' => ['nullable', 'string', 'max:255'],
            // 'addresses.*.country_code' => ['nullable', 'exists:countries,code'],
            // 'addresses.*.latitude' => ['nullable', 'numeric'],
            // 'addresses.*.longitude' => ['nullable', 'numeric'],
            // 'addresses.*.notes' => ['nullable', 'string', 'max:255'],

            // // User Bank Connections
            // 'bank_connections' => ['nullable', 'array'],
            // 'bank_connections.*.type' => ['required', 'string', 'max:255'],
            // 'bank_connections.*.bank_name' => ['nullable', 'string', 'max:255'],
            // 'bank_connections.*.branch' => ['nullable', 'string', 'max:255'],
            // 'bank_connections.*.account_name' => ['nullable', 'string', 'max:255'],
            // 'bank_connections.*.account_number' => ['nullable', 'string', 'max:255'],
            // 'bank_connections.*.swift_code' => ['nullable', 'string', 'max:255'],
            // 'bank_connections.*.iban' => ['nullable', 'string', 'max:255'],

            // // User Emails
            // 'emails' => ['nullable', 'array'],
            // 'emails.*.type' => ['required', 'string', 'max:255'],
            // 'emails.*.email' => ['required', 'string', 'email', 'max:255'],

            // // User Phonenumbers
            // 'phonenumbers' => ['nullable', 'array'],
            // 'phonenumbers.*.type' => ['required', 'string', 'max:255'],
            // 'phonenumbers.*.number' => ['required', 'string', 'max:255'],

            // // User Dates
            // 'dates' => ['nullable', 'array'],
            // 'dates.*.type' => ['required', 'string', 'max:255'],
            // 'dates.*.date' => ['required', 'date'],
            // 'dates.*.ignore_year' => ['nullable', 'boolean'],
            // 'dates.*.repeats_annually' => ['nullable', 'boolean'],

            // // User Links
            // 'links' => ['nullable', 'array'],
            // 'links.*.name' => ['required', 'string', 'max:255'],
            // 'links.*.url' => ['required', 'string', 'max:255'],
        ];
    }
}
