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
        foreach ($this->items as $key => $item) {
            $this->items[$key]['guard_name'] = 'web';

            if (!is_string($item['permissions'])) continue;

            $this->items[$key]['permissions'] = explode(',', $item['permissions']);
        }
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
