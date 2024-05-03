<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicUserResource extends JsonResource
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
            'profile_image' => $this->profile_image,
            'name' => $this->name,
            'username' => $this->username,
            // If data includes pivot
            'pivot' => $this->pivot ? $this->pivot->toArray() : null,
        ];
    }
}
