<?php

namespace App\Http\Resources\ContentPost;

use App\Http\Resources\AccessResource;
use App\Http\Resources\ContentSpace\BasicContentSpaceResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentPostGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner' => BasicUserResource::make($this->owner),
            'space' => BasicContentSpaceResource::make($this->space),
            'post' => ContentPostResource::make($this->post),
            'draft' => ContentPostResource::make($this->draft),
            'hidden' => $this->hidden,
            'access' => AccessResource::collection($this->accesses),
        ];
    }
}
