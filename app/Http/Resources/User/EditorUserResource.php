<?php

namespace App\Http\Resources\User;

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
                'profile_image' => $this->profile_image,
                'email' => $this->email,
                'name' => $this->name,
                'username' => $this->username,
                'ident_number' => $this->ident_number,
                'email_verified_at' => $this->email_verified_at,
                'enabled_at' => $this->enabled_at,
                'deleted_at' => $this->deleted_at,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],

            'user_name' => $this->user_name,
            'user_company' => $this->user_company,
            'addresses' => $this->addresses,
            'bank_connections' => $this->bank_connections,

            'roles' => $this->roles,
            'permissions' => $this->permissions->pluck('name'),
        ];
    }
}
