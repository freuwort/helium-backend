<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasicUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'profile_image' => $this->profile_image,
            'profile_banner' => $this->profile_banner,
            'name' => $this->user_name->fullname ?? $this->name ?? '',
            'username' => $this->username,
        ];
    }
}
