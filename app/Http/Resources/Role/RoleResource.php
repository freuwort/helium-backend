<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'icon' => $this->icon,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'uses' => [
                'users' => $this->users->count(),
                'accesses' => $this->accesses->count(),
            ],

            'is_admin' => $this->is_admin,
            'has_forbidden_permissions' => $this->has_forbidden_permissions,
            'has_elevated_permissions' => $this->has_elevated_permissions,
            
            'permissions' => $this->permissions->pluck('name'),
        ];
    }
}
