<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\AccessResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'model' => [
                'parent_id' => $this->parent_id,
                'parent' => BasicCategoryResource::make($this->parent),
                'inherit_access' => $this->inherit_access,
                'owner' => BasicUserResource::make($this->owner),
                'type' => $this->type,
                'name' => $this->name,
                'slug' => $this->slug,
                'content' => $this->content,
                'icon' => $this->icon,
                'color' => $this->color,
                'hidden' => $this->hidden,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            
            'access' => AccessResource::collection($this->accesses),
        ];
    }
}
