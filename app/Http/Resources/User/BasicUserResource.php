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
            'avatar' => $this->getProfileMedia('avatar'),
            'banner' => $this->getProfileMedia('banner'),
            'name' => $this->name ?? '',
            'username' => $this->username,
        ];
    }
}
