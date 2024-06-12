<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Form\BasicFormResource;
use App\Http\Resources\Form\BasicFormSubmissionResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasicEventInviteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => BasicEventResource::make($this->event),
            'user' => BasicUserResource::make($this->user),
            'form' => BasicFormResource::make($this->form),
            'type' => $this->type,
            'needs_claim' => $this->needs_claim,
            'name' => $this->name,
            'status' => $this->status,
            'details' => BasicFormSubmissionResource::collection($this->submissions),
        ];
    }
}
