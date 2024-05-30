<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // User Model
            'model.name' => ['nullable', 'string', 'max:255'],
            'model.username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'model.email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'model.email_verified_at' => ['nullable', 'date'],
            'model.enabled_at' => ['nullable', 'date'],

            // User Password
            'password' => ['nullable', 'string', 'min:8'],

            // User Roles
            'roles' => ['nullable', 'array'],
            'roles.*' => ['required', 'exists:roles,id'],

            // User Name
            'user_name.salutation' => ['nullable', 'string', 'max:255'],
            'user_name.prefix' => ['nullable', 'string', 'max:255'],
            'user_name.firstname' => ['nullable', 'string', 'max:255'],
            'user_name.middlename' => ['nullable', 'string', 'max:255'],
            'user_name.lastname' => ['nullable', 'string', 'max:255'],
            'user_name.suffix' => ['nullable', 'string', 'max:255'],
            'user_name.legalname' => ['nullable', 'string', 'max:255'],
            'user_name.nickname' => ['nullable', 'string', 'max:255'],

            // User Company
            'user_company.company' => ['nullable', 'string', 'max:255'],
            'user_company.department' => ['nullable', 'string', 'max:255'],
            'user_company.title' => ['nullable', 'string', 'max:255'],

            // User Identifiers
            'identifiers' => ['nullable', 'array'],
            'identifiers.*.type' => ['required', 'string', 'max:255'],
            'identifiers.*.label' => ['required', 'string', 'max:255'],
            'identifiers.*.value' => ['required', 'string', 'max:255'],

            // User Addresses
            'addresses' => ['nullable', 'array'],
            'addresses.*.type' => ['required', 'string', 'max:255'],
            'addresses.*.address_line_1' => ['nullable', 'string', 'max:255'],
            'addresses.*.address_line_2' => ['nullable', 'string', 'max:255'],
            'addresses.*.city' => ['nullable', 'string', 'max:255'],
            'addresses.*.state' => ['nullable', 'string', 'max:255'],
            'addresses.*.postal_code' => ['nullable', 'string', 'max:255'],
            'addresses.*.country_code' => ['nullable', 'exists:countries,code'],
            'addresses.*.latitude' => ['nullable', 'numeric'],
            'addresses.*.longitude' => ['nullable', 'numeric'],
            'addresses.*.notes' => ['nullable', 'string', 'max:255'],

            // User Bank Connections
            'bank_connections' => ['nullable', 'array'],
            'bank_connections.*.type' => ['required', 'string', 'max:255'],
            'bank_connections.*.bank_name' => ['nullable', 'string', 'max:255'],
            'bank_connections.*.branch' => ['nullable', 'string', 'max:255'],
            'bank_connections.*.account_name' => ['nullable', 'string', 'max:255'],
            'bank_connections.*.account_number' => ['nullable', 'string', 'max:255'],
            'bank_connections.*.swift_code' => ['nullable', 'string', 'max:255'],
            'bank_connections.*.iban' => ['nullable', 'string', 'max:255'],

            // User Emails
            'emails' => ['nullable', 'array'],
            'emails.*.type' => ['required', 'string', 'max:255'],
            'emails.*.email' => ['required', 'string', 'email', 'max:255'],

            // User Phonenumbers
            'phonenumbers' => ['nullable', 'array'],
            'phonenumbers.*.type' => ['required', 'string', 'max:255'],
            'phonenumbers.*.number' => ['required', 'string', 'max:255'],

            // User Dates
            'dates' => ['nullable', 'array'],
            'dates.*.type' => ['required', 'string', 'max:255'],
            'dates.*.date' => ['required', 'date'],
            'dates.*.ignore_year' => ['nullable', 'boolean'],
            'dates.*.repeats_annually' => ['nullable', 'boolean'],

            // User Links
            'links' => ['nullable', 'array'],
            'links.*.name' => ['required', 'string', 'max:255'],
            'links.*.url' => ['required', 'string', 'max:255'],
        ];
    }
}
