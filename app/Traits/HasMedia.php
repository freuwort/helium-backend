<?php

namespace App\Traits;

use App\Models\Media;

trait HasMedia
{
    public function media()
    {
        return $this->morphToMany(Media::class, 'model', 'model_has_media');
    }



    public function syncMedia($media)
    {
        $this->media()->sync($media);
    }
}