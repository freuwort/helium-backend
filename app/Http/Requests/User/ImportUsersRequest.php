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
                'user_info' => collect($item)
                    ->filter(fn ($value, $key) => str_starts_with($key, 'user_info_'))
                    ->reduce(function ($carry, $value, $key) {
                        $newKey = str_replace('user_info_', '', $key);
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
            'items.*.phone' => ['nullable', 'string', 'max:255'],
            'items.*.password' => ['nullable', 'string', 'max:255'],
            'items.*.email_verified_at' => ['nullable', 'date'],
            'items.*.phone_verified_at' => ['nullable', 'date'],
            'items.*.enabled_at' => ['nullable', 'date'],

            // User Roles
            'items.*.roles' => ['nullable', 'array'],
            'items.*.roles.*' => ['required', 'exists:roles,id'],

            // User Info
            'items.*.user_info.salutation' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.prefix' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.firstname' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.middlename' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.lastname' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.suffix' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.legalname' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.nickname' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.organisation' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.department' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.job_title' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.customer_id' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.employee_id' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.member_id' => ['nullable', 'string', 'max:255'],
            'items.*.user_info.notes' => ['nullable', 'string', 'max:1023'],
        ];
    }
}
