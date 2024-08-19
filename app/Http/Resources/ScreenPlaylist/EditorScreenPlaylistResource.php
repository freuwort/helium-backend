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

            'screens' => $this->screens->map(function ($screen) {
                return [
                    'id' => $screen->id,
                    'name' => $screen->name,
                    'from_date' => $screen->pivot->from_date,
                    'from_time' => $screen->pivot->from_time,
                    'to_date' => $screen->pivot->to_date,
                    'to_time' => $screen->pivot->to_time,
                    'on_days' => $screen->pivot->on_days,
                ];
            }),
        ];
    }
}
