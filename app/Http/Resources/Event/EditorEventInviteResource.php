<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Form\BasicFormResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorEventInviteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'model' => [
                'event_id' => $this->event_id,
                'event' => BasicEventResource::make($this->event),
                'user_id' => $this->user_id,
                'user' => BasicUserResource::make($this->user),
                'form_id' => $this->form_id,
                'form' => BasicFormResource::make($this->form),
                'type' => $this->type,
                'needs_claim' => $this->needs_claim,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'code' => $this->code,
                'status' => $this->status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
