<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Address\BasicAddressResource;
use App\Http\Resources\Media\PivotMediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasicEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'main_address' => BasicAddressResource::make($this->main_address),
            'header_media' => PivotMediaResource::make($this->header_media),
            'logo_media' => PivotMediaResource::make($this->logo_media),
        ];
    }
}
