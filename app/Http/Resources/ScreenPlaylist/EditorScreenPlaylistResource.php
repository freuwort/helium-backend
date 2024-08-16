<?php

namespace App\Http\Resources\ScreenPlaylist;

use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorScreenPlaylistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'model' => [
                'owner' => BasicUserResource::make($this->owner),
                'type' => $this->type,
                'name' => $this->name,
                'screen_order' => $this->screen_order,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
