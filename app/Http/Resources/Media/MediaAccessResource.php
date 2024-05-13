<?php

namespace App\Http\Resources\Media;

use App\Http\Resources\Role\BasicRoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\BasicUserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MediaAccessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'media_id' => $this->media_id,
            'model_id' => $this->model_id,
            'model_type' => $this->typeDict($this->model_type),
            'model' => $this->modelDict($this->model),
            'type' => $this->type,
            'permission' => $this->permission,
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
