<?php

namespace App\Http\Resources\Media;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'drive' => $this->drive,
            'src_path' => $this->src_path,
            'cdn_path' => $this->cdn_path,
            'thumbnail_path' => $this->thumbnail_path,
            'mime_type' => $this->mime_type,
            'name' => $this->name,
            'access' => $this->access,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
