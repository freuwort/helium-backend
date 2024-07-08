<?php

namespace App\Http\Resources\ContentPost;

use App\Http\Resources\AccessResource;
use App\Http\Resources\ContentSpace\BasicContentSpaceResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorContentPostGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'model' => [
                'owner' => BasicUserResource::make($this->owner),
                'space' => BasicContentSpaceResource::make($this->space),
                'post_id' => $this->post_id,
                'hidden' => $this->hidden,
            ],
            'draft' => ContentPostResource::make($this->draft),
            'posts' => ContentPostResource::collection($this->posts),
            'access' => AccessResource::collection($this->accesses),
        ];
    }
}
