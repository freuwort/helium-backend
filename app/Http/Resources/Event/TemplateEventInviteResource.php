<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\User\BasicUserResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateEventInviteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $event = $this->event;
        $user = $this->user;

        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'event_name' => $event->name,
            'event_slug' => $event->slug,
            'event_start_at' => $event->start_at,
            'event_end_at' => $event->end_at,
            'user_id' => $this->user_id,
            'user_name' => optional(optional($user)->user_name)->fullname ?? optional($user)->name,
            'type' => $this->type,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'code' => $this->code,
            'invite_link' => '<a target="_blank" href="'.$this->invite_link.'">Event Einladung</a>',
            'status' => $this->status ?? 'pending',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
