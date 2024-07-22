<?php

namespace App\Http\Requests\Role;

use App\Classes\Permissions\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // If user wants to create a super admin: deny
        if (in_array(Permissions::SYSTEM_SUPER_ADMIN, $this->permissions)) return false;

        // Check specific permissions against needed permissions
        if (in_array(Permissions::SYSTEM_ADMIN, $this->permissions)         && !$this->user()->can(Permissions::SYSTEM_ADMIN)) return false;
        if (in_array(Permissions::SYSTEM_VIEW_ROLES, $this->permissions)    && !$this->user()->can(Permissions::SYSTEM_ADMIN)) return false;
        if (in_array(Permissions::SYSTEM_CREATE_ROLES, $this->permissions)  && !$this->user()->can(Permissions::SYSTEM_ADMIN)) return false;
        if (in_array(Permissions::SYSTEM_EDIT_ROLES, $this->permissions)    && !$this->user()->can(Permissions::SYSTEM_ADMIN)) return false;
        if (in_array(Permissions::SYSTEM_DELETE_ROLES, $this->permissions)  && !$this->user()->can(Permissions::SYSTEM_ADMIN)) return false;
        if (in_array(Permissions::SYSTEM_ASSIGN_ROLES, $this->permissions)  && !$this->user()->can(Permissions::SYSTEM_ADMIN)) return false;

        // Otherwise: allow
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:31'],
            'icon' => ['nullable', 'string', 'max:63'],
            'permissions' => ['nullable', 'array',],
            'permissions.*' => ['required', 'distinct', 'exists:permissions,name'],
        ];
    }
}
