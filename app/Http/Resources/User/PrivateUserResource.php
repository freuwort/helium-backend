<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateUserResource extends JsonResource
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
            'image' => 'https://avatar.iran.liara.run/public/?username=' . $this->name . '&size=80',
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'ident_number' => $this->ident_number,
            'email_verified_at' => $this->email_verified_at,
            'enabled_at' => $this->enabled_at,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
