<?php

namespace App\Http\Requests\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateManyUserRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*' => ['required', 'exists:users,id'],
            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'exists:roles,name'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'items' => User::whereIn('id', $this->items)->get(),
            'roles' => Role::whereIn('name', $this->roles)->get(),
        ]);
    }
}
