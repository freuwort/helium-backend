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



    // START: Selectors
    public static function findPath(string $path): ?Media
    {
        return self::where('src_path', $path)->firstOrFail();
    }
    // END: Selectors



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



    public function getCdnPathAttribute(): string
    {
        return config('app.url') . '/media/' . $this->src_path;
    }



    public static function discovery(string $path)
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

        // When subfolder exists: check if the parent media model exists
        if ($path->hasSubfolder && !$parent)
        {
            throw new \Exception('The parent folder does not exist.');
        }



        // Get all files and folders in the path
        $files = Storage::files($path->path);
        $folders = Storage::directories($path->path);

        // Loop through all files
        foreach ($files as $file)
        {
            // Check if the file already exists in the database
            $media = self::where('src_path', $file)->first();

            // If it doesn't exist, create it
            if (!$media)
            {
                $media = self::create([
                    'parent_id' => optional($parent)->id,
                    'drive' => $path->diskname,
                    'src_path' => $file,
                    'name' => Str::afterLast($file, '/'),
                    'mime_type' => Storage::mimeType($file),
                    'meta' => [
                        'extension' => Str::afterLast($file, '.'),
                        'size' => Storage::size($file),
                    ],
                ]);
            }
        }

        // Loop through all folders
        foreach ($folders as $folder)
        {
            // Check if the folder already exists in the database
            $media = self::where('src_path', $folder)->first();

            // If it doesn't exist, create it
            if (!$media)
            {
                $media = self::create([
                    'parent_id' => optional($parent)->id,
                    'drive' => $path->diskname,
                    'src_path' => $folder,
                    'name' => Str::afterLast($folder, '/'),
                    'mime_type' => 'folder',
                    'meta' => [
                        'extension' => null,
                        'size' => null,
                    ],
                ]);
            }

            // Recursively discover the folder
            self::discovery($folder);
        }
    }



    public static function upload(string $path, UploadedFile $file, string $name = null): Media
    {
        // Check if a file was uploaded
        if (!$file)
        {
            throw new \Exception('No file was uploaded.');
        }

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

        $this->move($oldPath->filepath, $newName);

        return $this->fresh();
    }



    public function move(string $destinationPath, string $name = null): Media
    {
        // Parse paths
        $oldPath = $this->dissectPath($this->src_path);
        $newPath = $this->dissectPath($destinationPath.'/'.($name ?? $oldPath->filename));
        $disk = self::getMediaDisk($newPath->diskname);
        $parent = self::getFolder($newPath->filepath);

        // Check if the disk exists and is allowed for media
        if (!$disk)
        {
            throw new \Exception('The disk "' . $newPath->diskname . '" does not exist or is not allowed for media.');
        }

        // When subfolder exists: check if the parent media model exists
        // (We dissect the path again because the dissect method assumes the path doesn't include a filename)
        if ($this->dissectPath($newPath->filepath)->hasSubfolder && !$parent)
        {
            throw new \Exception('The parent folder does not exist.');
        }

        // Check if the new path already exists
        if (Storage::exists($newPath->path))
        {
            throw new \Exception('The media already exists in destination.');
        }



        // Try moving the file on disk
        if (!Storage::move($oldPath->path, $newPath->path))
        {
            throw new \Exception('The media could not be moved to destination.');
        }

        // Update the media model
        $this->update([
            'parent_id' => optional($parent)->id,
            'drive' => $newPath->diskname,
            'src_path' => $newPath->path,
            'name' => $newPath->filename,
        ]);

        // Update the path of all children
        $this->recursivePathUpdate($oldPath->path, $newPath->path);

        // Return the media model
        return $this->fresh();
    }



    public function copy(string $newPath): Media
    {
        // Parse paths
        $oldPath = $this->dissectPath($this->src_path);
        $newPath = $this->dissectPath($newPath);
        $disk = self::getMediaDisk($newPath->diskname);
        $parent = self::getFolder($newPath->filepath);

        // Check if the disk exists and is allowed for media
        if (!$disk)
        {
            throw new \Exception('The disk "' . $newPath->diskname . '" does not exist or is not allowed for media.');
        }

        // When subfolder exists: check if the parent media model exists
        // (We dissect the path again because the dissect method assumes the path doesn't include a filename)
        if ($this->dissectPath($newPath->filepath)->hasSubfolder && !$parent)
        {
            throw new \Exception('The parent folder does not exist.');
        }

        // Check if media is a folder
        if ($this->mime_type == 'folder')
        {
            throw new \Exception('The media is a folder and cannot be copied.');
        }

        // Check if the new path already exists
        if (Storage::exists($newPath->path))
        {
            throw new \Exception('The media already exists in destination.');
        }



        // Try copying the file on disk
        // Todo: implement folder copying
        if (!Storage::copy($oldPath->path, $newPath->path))
        {
            throw new \Exception('The media could not be copied to destination.');
        }

        // Copy the media model recursively
        $copy = $this->copyModelRecursive($parent, $newPath->path);

        // Return the media model
        return $copy;
    }



    private function copyModelRecursive(?Media $parent, string $newPath): Model
    {
        // Parse paths
        $newPath = $this->dissectPath($newPath);

        // Copy self
        $newModel = $this->replicate()->fill([
            'parent_id' => optional($parent)->id,
            'drive' => $newPath->diskname,
            'src_path' => $newPath->path,
            'name' => $newPath->filename,
        ]);

        // Save the new model
        $newModel->save();

        // Copy its children
        $children = $this->children()->get();

        foreach ($children as $child)
        {
            $child->copyModelRecursive($this, $newPath->path . '/' . $child->name);
        }

        // Return the new model
        return $newModel;
    }
}
