<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Http\UploadedFile;

trait HasMedia
{
    // START: Relationships
    public function media()
    {
        return $this->morphToMany(Media::class, 'model', 'model_has_media')->withPivot('type');
    }
    // END: Relationships



    
    
    
    // START: Getters
    public function getDefaultProfileMedia($type)
    {
        return url(route('default.image', [$type, 'Unknown']));
    }

    public function getProfileMedia($type)
    {
        $media = $this->media->filter(fn ($e) => $e->pivot->type == $type)->first();

        // Return default image if no media is found
        if (!$media) return $this->getDefaultProfileMedia($type);


        return $media->cdn_path;
    }
    // END: Getters



    // START: Profile media
    public function uploadProfileMedia(UploadedFile $file, String $type)
    {
        if (!in_array($type, $this->media_types)) return;

        // Delete old profile media
        $this->media()->where('type', $type)->get()->each(function ($media) {
            $media->delete();
        });        

        // Upload new profile media
        $media = Media::upload('profiles', $file);

        // Set profile image
        $this->media()->syncWithoutDetaching([[
            'media_id' => $media->id,
            'type' => $type,
        ]]);
    }
    // END: Profile media
}