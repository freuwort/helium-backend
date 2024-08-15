<?php

namespace App\Http\Resources\ScreenDevice;

use App\Http\Resources\Address\BasicAddressResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScreenDeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner' => BasicUserResource::make($this->owner),
            'name' => $this->name,
            'address' => BasicAddressResource::make($this->address),
            'group' => $this->group,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
