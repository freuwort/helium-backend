<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class ImportRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    

    public function prepareForValidation(): void
    {
        $this->merge(['items' => array_map(fn ($item) => [
            ...$item,
            'guard_name' => 'web',
            'permissions' => is_string($item['permissions']) ? explode(',', $item['permissions']) : $item['permissions'],
        ], $this->items)]);
    }

    
    
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.color' => ['nullable', 'string', 'max:31'],
            'items.*.icon' => ['nullable', 'string', 'max:63'],
            'items.*.guard_name' => ['nullable', 'string', 'in:web'],
            'items.*.permissions' => ['nullable', 'array'],
            'items.*.permissions.*' => ['required', 'distinct', 'exists:permissions,name'],
        ];
    }
}
