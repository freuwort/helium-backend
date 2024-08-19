<?php

namespace App\Http\Resources\ScreenDevice;

use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorScreenDeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'model' => [
                'owner' => BasicUserResource::make($this->owner),
                'name' => $this->name,
                'group' => $this->group,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            
            'address' => $this->address,

            'playlists' => $this->playlists->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'from_date' => $playlist->pivot->from_date,
                    'from_time' => $playlist->pivot->from_time,
                    'to_date' => $playlist->pivot->to_date,
                    'to_time' => $playlist->pivot->to_time,
                    'on_days' => $playlist->pivot->on_days,
                    'on_screen' => $playlist->pivot->on_screen,
                ];
            }),
        ];
    }
}
