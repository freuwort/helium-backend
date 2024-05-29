<?php

namespace App\Http\Resources;

use App\Http\Resources\Role\BasicRoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\BasicUserResource;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;

class AccessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'accessable_id' => $this->accessable_id,
            'accessable_type' => $this->typeDict($this->accessable_type),

            'permissible_id' => $this->permissible_id,
            'permissible_type' => $this->typeDict($this->permissible_type),
            'permissible' => $this->modelDict($this->permissible),

            'type' => $this->type,
            'permission' => $this->permission,
        ];
    }



    private function typeDict($type)
    {
        return match ($type) {
            Media::class => 'media',
            User::class => 'user',
            Role::class => 'role',
            default => null
        };
    }

    private function modelDict($model)
    {
        if (!$model) return null;
        
        return match (get_class($model)) {
            User::class => BasicUserResource::make($model),
            Role::class => BasicRoleResource::make($model),
            default => null
        };
    }
}
