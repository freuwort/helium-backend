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
            'items.*.phone' => ['nullable', 'string', 'max:255'],
            'items.*.password' => ['nullable', 'string', 'max:255'],
            'items.*.email_verified_at' => ['nullable', 'date'],
            'items.*.phone_verified_at' => ['nullable', 'date'],
            'items.*.enabled_at' => ['nullable', 'date'],
            
            'items.*.salutation' => ['nullable', 'string', 'max:255'],
            'items.*.prefix' => ['nullable', 'string', 'max:255'],
            'items.*.firstname' => ['nullable', 'string', 'max:255'],
            'items.*.middlename' => ['nullable', 'string', 'max:255'],
            'items.*.lastname' => ['nullable', 'string', 'max:255'],
            'items.*.suffix' => ['nullable', 'string', 'max:255'],
            'items.*.legalname' => ['nullable', 'string', 'max:255'],
            'items.*.nickname' => ['nullable', 'string', 'max:255'],
            
            'items.*.organisation' => ['nullable', 'string', 'max:255'],
            'items.*.department' => ['nullable', 'string', 'max:255'],
            'items.*.job_title' => ['nullable', 'string', 'max:255'],

            'items.*.customer_id' => ['nullable', 'string', 'max:255'],
            'items.*.employee_id' => ['nullable', 'string', 'max:255'],
            'items.*.member_id' => ['nullable', 'string', 'max:255'],

            'items.*.notes' => ['nullable', 'string', 'max:1023'],
            
            // User Roles
            'items.*.roles' => ['nullable', 'array'],
            'items.*.roles.*' => ['required', 'exists:roles,id'],
        ];
    }
}
