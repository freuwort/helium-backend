<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateEventInviteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $event = $this->event;
        $form = $this->form;

        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'code' => $this->code,
            'invite_link' => $this->invite_link,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'event_id' => $this->event_id,
            'event_name' => $event->name,
            'event_slug' => $event->slug,
            'event_start_at' => $event->start_at,
            'event_end_at' => $event->end_at,

            'form_id' => $this->form_id,
            'form_name' => $form->name,
            'form_slug' => $form->slug,
        ];
    }
}
