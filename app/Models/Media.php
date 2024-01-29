<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

        self::created(function ($model) {
            // Queue thumbnail generation
        });

        self::deleting(function ($model) {
            // Check if the model is a folder
            if (!Storage::mimeType($model->src_path)) Storage::deleteDirectory($model->src_path);
            // Otherwise, delete the file from disk
            else Storage::delete($model->src_path);
        });
    }



    // START: Relationships
    public function parent()
    {
        return $this->belongsTo(Media::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Media::class, 'parent_id')->orderByRaw("FIELD(mime_type , 'folder') DESC")->orderBy('src_path', 'asc');
    }
    // END: Relationships



    public static function dissectPath(string $path): object
    {
        return (object) [
            'path' => $path,
            'diskname' => Str::before($path, '/'),
            'hasSubfolder' => count(explode('/', $path)) > 1,
            'subfolder' => Str::after($path, '/'),
            'filename' => Str::afterLast($path, '/'),
            'filepath' => Str::beforeLast($path, '/'),
        ];
    }



    public static function getMediaDisk(string $diskname): ?object
    {
        if (!$diskname) return null;

        $disks = config('filesystems.disks');

        foreach ($disks as $key => $value)
        {
            if ($key == $diskname && $value['use_for_media']) return (object) $value;
        }

        return null;
    }



    public static function getFolder(string $path): ?Media
    {
        return self::where('src_path', $path)->where('mime_type', 'folder')->first();
    }



    public static function upload(string $path, UploadedFile $file, string $name = null): Media
    {
        // Check if a file was uploaded
        if (!$file) throw new \Exception('No file was uploaded.');

        // Parse path
        $path = self::dissectPath($path);
        $disk = self::getMediaDisk($path->diskname);
        $parent = self::getFolder($path->path);

        // Check if the disk exists and is allowed for media
        if (!$disk)
        {
            throw new \Exception('The disk "' . $path->diskname . '" does not exist or is not allowed for media.');
        }

        // When subfolder exists: check if the disk allows subfolder creation
        if ($path->hasSubfolder && !$disk->allow_subfolder_creation)
        {
            throw new \Exception('The disk "' . $path->diskname . '" does not allow subfolder creation.');
        }

        // When subfolder exists: check if the parent media model exists
        if ($path->hasSubfolder && !$parent)
        {
            throw new \Exception('The parent folder does not exist.');
        }



        // Get meta data
        $mime = $file->getMimeType();
        $hashname = $file->hashName();
        $originalname = $file->getClientOriginalName();
        $extension = $file->extension();
        $size = $file->getSize();

        // Make name depending on whether the disk allows custom names - fallback order: $name, $originalname, $hashname
        $name = $disk->allow_custom_filename ? $name ?? $originalname ?? $hashname : $hashname;



        // Store the file on disk
        $storagePath = Storage::putFileAs($path->path, $file, $name);

        // Create or update the media model
        $media = self::updateOrCreate([
            'src_path' => $storagePath,
        ],[
            'parent_id' => optional($parent)->id,
            'drive' => $path->diskname,
            'mime_type' => $mime,
            'name' => $name,
            'meta' => [
                'extension' => $extension,
                'size' => $size,
            ],
        ]);



        // Return the media model
        return $media->fresh();
    }



    public static function createFolder(string $path, string $name): Media
    {
        // Parse path
        $path = self::dissectPath($path);
        $disk = self::getMediaDisk($path->diskname);
        $parent = self::getFolder($path->path);

        // Check if the disk exists and is allowed for media
        if (!$disk)
        {
            throw new \Exception('The disk "' . $path->diskname . '" does not exist or is not allowed for media.');
        }

        // Check if the disk allows subfolder creation
        if (!$disk->allow_subfolder_creation)
        {
            throw new \Exception('The disk "' . $path->diskname . '" does not allow subfolder creation.');
        }

        // When subfolder exists: check if the parent media model exists
        if ($path->hasSubfolder && !$parent)
        {
            throw new \Exception('The parent folder does not exist.');
        }

        
        
        // Create the storage path
        $storagePath = $path->path . '/' . $name;

        // Create the folder on disk
        Storage::makeDirectory($storagePath);

        // Create or update the media model
        $media = self::updateOrCreate([
            'src_path' => $storagePath,
        ],[
            'parent_id' => optional($parent)->id,
            'drive' => $path->diskname,
            'mime_type' => 'folder',
            'name' => $name,
            'meta' => [
                'extension' => null,
                'size' => null,
            ],
        ]);



        // Return the media model
        return $media->fresh();
    }



    public function rename(string $newName): Media
    {
        $oldPath = $this->dissectPath($this->src_path);
        $newPath = $oldPath->filepath . '/' . $newName;

        $this->move($newPath);

        return $this->fresh();
    }



    public function move(string $newPath): Media
    {
        $oldPath = $this->dissectPath($this->src_path);
        $newPath = $this->dissectPath($newPath);

        // Move the file on disk
        Storage::move($oldPath->path, $newPath->path);

        // Update the media model
        $this->update([
            'name' => $newPath->filename,
            'src_path' => $newPath->path,
        ]);

        // Update the path of all children
        $this->recursivePathUpdate($oldPath->path, $newPath->path);

        // Return the media model
        return $this->fresh();
    }



    private function recursivePathUpdate(string $oldPath, string $newPath): void
    {
        $children = $this->children()->get();

        foreach ($children as $child)
        {
            $child->update([
                'src_path' => Str::replaceFirst($oldPath, $newPath, $child->src_path),
            ]);

            $child->recursivePathUpdate($oldPath, $newPath);
        }
    }
}
