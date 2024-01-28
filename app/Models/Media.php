<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;



class Media extends Model
{
    use HasRoles, HasFactory;

    protected $fillable = [
        'parent_id',
        'drive',
        'src_path',
        'thumbnail_path',
        'mime_type',
        'name',
        'access',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];



    public static function boot()
    {
        parent::boot();

        // self::created(function ($model) {
        //     // Queue thumbnail generation
        // });

        // self::deleting(function ($model) {
        //     // Check if the model is a folder
        //     if (!Storage::mimeType($model->path)) Storage::deleteDirectory($model->path);
        //     // Otherwise, delete the file from disk
        //     else Storage::delete($model->path);

        //     // And delete the thumbnail if it exists
        //     if ($model->thumbnail_path) Storage::delete($model->thumbnail_path);
        // });
    }



    // START: Relationships
    public function parent()
    {
        return $this->belongsTo(Media::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Media::class, 'parent_id')->orderByRaw("FIELD(mime_type , 'folder') DESC")->orderBy('path', 'asc');
    }

    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'media_user');
    // }

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'media_role');
    // }
    // END: Relationships



    public static function getMediaDisk($disk)
    {
        $disks = config('filesystems.disks');

        foreach ($disks as $key => $value)
        {
            if ($key == $disk && $value['use_for_media']) return $value;
        }

        return null;
    }



    public static function upload($file, $fullpath, $name = null)
    {
        // Get meta data
        $mime = $file->getMimeType();
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $size = $file->getSize();
        $hash = md5_file($file->getRealPath());

        $path = explode('/', $fullpath);
        $diskName = $path[0];
        $subDirectory = implode('/', array_slice($path, 1));

        // Get disk from path
        $disk = self::getMediaDisk($diskName);

        if (!$disk) throw new \Exception('Invalid disk');
        if (!$disk['allow_subfolder_creation'] && $subDirectory) throw new \Exception('Subfolder creation not allowed');
        if (!$disk['allow_custom_filename']) $name = $hash.'.'.$extension;

        // Get the store path
        $storePath = $file->storeAs($subDirectory, $name, $diskName);
        
        

        // Create the file in the database
        $media = self::updateOrCreate([
            'src_path' => $storePath,
        ],[
            'drive' => config('filesystems.default'),
            'mime_type' => $mime,
            'name' => $name,
            'access' => 'private',
            'meta' => [
                'extension' => $extension,
                'size' => $size,
                'disk' => $disk,
            ],
        ]);

        return $media->fresh();
    }
}
