<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\BasicRoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'model' => [
                'avatar' => $this->getProfileMedia('avatar'),
                'banner' => $this->getProfileMedia('banner'),
                'email' => $this->email,
                'phone' => $this->phone,
                'username' => $this->username,
                'requires_password_change' => $this->requires_password_change,
                'requires_two_factor' => $this->requires_two_factor,
                'email_verified_at' => $this->email_verified_at,
                'phone_verified_at' => $this->phone_verified_at,
                'enabled_at' => $this->enabled_at,
                'deleted_at' => $this->deleted_at,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],

            'user_info' => $this->user_info,

            'roles' => BasicRoleResource::collection($this->roles),
            'permissions' => $this->permissions->pluck('name'),
        ];
    }
}
