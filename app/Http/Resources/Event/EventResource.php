<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\AccessResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'inherit_access' => $this->inherit_access,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'invites_count' => $this->invites()->count(),

            'addresses' => $this->addresses,
            'access' => AccessResource::collection($this->accesses),
        ];
    }
}
