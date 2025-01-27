<?php

namespace App\Http\Resources\Device;

use App\Http\Resources\Address\BasicAddressResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'group' => $this->group,
            'owner' => BasicUserResource::make($this->owner),
            'address' => BasicAddressResource::make($this->address),
            'os_platform' => $this->os_platform,
            'os_arch' => $this->os_arch,
            'os_release' => $this->os_release,
            'app_version' => $this->app_version,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
