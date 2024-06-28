<?php

namespace App\Http\Resources\ContentSpace;

use App\Http\Resources\AccessResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentSpaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'inherit_access' => $this->inherit_access,
            'owner' => BasicUserResource::make($this->owner),
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent' => BasicContentSpaceResource::make($this->parent),
            'access' => AccessResource::collection($this->accesses),
        ];
    }
}
