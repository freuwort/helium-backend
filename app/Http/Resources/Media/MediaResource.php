<?php

namespace App\Http\Resources\Media;

use App\Http\Resources\AccessResource;
use App\Http\Resources\Role\BasicRoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\BasicUserResource;
use App\Models\Role;
use App\Models\User;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'drive' => $this->drive,
            'src_path' => $this->src_path,
            'cdn_path' => $this->cdn_path,
            'thumbnail' => $this->thumbnail,
            'mime_type' => $this->mime_type,
            'name' => $this->name,
            'owner_id' => $this->owner_id,
            'owner_type' => $this->typeDict($this->owner_type),
            'owner' => $this->modelDict($this->owner),
            'inherit_access' => $this->inherit_access,
            'access' => AccessResource::collection($this->accesses),
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'DEBUG' => [
                'computed_access' => $this->computeAccess(),
            ],
        ];
    }



    private function typeDict($type)
    {
        return match ($type) {
            User::class => 'user',
            Role::class => 'role',
            default => null
        };
    }

    private function modelDict($model)
    {
        return match (get_class($model)) {
            User::class => BasicUserResource::make($model),
            Role::class => BasicRoleResource::make($model),
            default => null
        };
    }
}
