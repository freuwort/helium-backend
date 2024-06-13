<?php

namespace App\Http\Resources\Media;

use App\Http\Resources\AccessResource;
use App\Http\Resources\Role\BasicRoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\BasicUserResource;
use App\Models\Role;
use App\Models\User;

class PivotMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'drive' => $this->drive,
            'src_path' => $this->src_path,
            'cdn_path' => $this->cdn_path,
            'thumbnail' => $this->thumbnail,
            'mime_type' => $this->mime_type,
            'name' => $this->name,
            'meta' => $this->meta,
            // get the extra pivot key named "type" (has to be loaded)
            'type' => $this->pivot->type,
        ];
    }
}
