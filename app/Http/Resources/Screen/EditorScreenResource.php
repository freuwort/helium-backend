<?php

namespace App\Http\Resources\Screen;

use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorScreenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'model' => [
                'owner' => BasicUserResource::make($this->owner),
                'name' => $this->name,
                'content' => $this->content,
                'background' => $this->background,
                'width' => $this->width,
                'height' => $this->height,
                'duration' => $this->duration,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
