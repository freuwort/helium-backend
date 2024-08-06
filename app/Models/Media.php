<?php

namespace App\Models;

use App\Traits\HasAccessControl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Intervention\Image\Laravel\Facades\Image;

class Media extends Model
{
    use HasFactory, HasAccessControl;

    protected $fillable = [
        'parent_id',
        'drive',
        'src_path',
        'thumbnail',
        'mime_type',
        'name',
        'owner_id',
        'owner_type',
        'inherit_access',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'inherit_access' => 'boolean',
    ];



    public static function boot()
    {
        parent::boot();

        self::deleting(function ($model) {
            !Storage::mimeType($model->src_path) ? Storage::deleteDirectory($model->src_path) : Storage::delete($model->src_path);
        });
    }



    // START: Relationships
    public function parent()
    {
        return $this->belongsTo(Media::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Media::class, 'parent_id')->orderByRaw("FIELD(mime_type , 'directory') DESC")->orderBy('src_path', 'asc');
    }

    public function owner()
    {
        return $this->morphTo();
    }
    // END: Relationships



    // START: Scopes
    public function scopeWhereChildOfPath($query, string $path)
    {
        $path = $this->dissectPath($path);
        
        if ($path->hasSubdirectory)
        {
            return $query
                ->where('src_path', $path->path)
                ->firstOrFail()
                ->children();
        }
        
        return $query
            ->where('drive', $path->diskname)
            ->where('parent_id', null);
    }
    
    public function scopeOrderByDefault($query)
    {
        return $query
            ->orderByRaw("FIELD(mime_type , 'directory') DESC")
            ->orderBy('src_path', 'asc');
    }
    // END: Scopes



    // START: Selectors
    public static function findPath(string $path): ?Media
    {
        return self::where('src_path', $path)->first();
    }

    public static function findPathOrFail(string $path): ?Media
    {
        return self::where('src_path', $path)->firstOrFail();
    }
    // END: Selectors



    public static function dissectPath(string $path): object
    {
        return (object) [
            'path' => $path,
            'diskname' => Str::before($path, '/'),
            'hasSubdirectory' => count(explode('/', $path)) > 1,
            'subdirectory' => Str::after($path, '/'),
            'filename' => Str::afterLast($path, '/'),
            'filepath' => Str::beforeLast($path, '/'),
        ];
    }

    public static function dissectFilename(string $filename): object
    {
        return (object) [
            'name' => Str::beforeLast($filename, '.'),
            'parts' => explode('.', Str::beforeLast($filename, '.')),
            'extension' => Str::afterLast($filename, '.'),
            'filename' => $filename,
        ];
    }



    public static function getMediaDisk(string|null $diskname): ?object
    {
        if (!$diskname) return null;

        $disks = config('filesystems.disks');

        foreach ($disks as $key => $value)
        {
            if ($key == $diskname && $value['use_for_media']) return (object) $value;
        }

        return null;
    }

    public static function getMediaDiskOrFail(string $diskname): object
    {
        return self::getMediaDisk($diskname) ?: throw new \Exception('The disk "' . $diskname . '" does not exist or is not allowed for media.');
    }



    // START: Access control
    public static function defaultAccess($accessable): Collection
    {
        $diskname = null;
        
        if ($accessable instanceof Media) $diskname = $accessable->drive;
        if (is_string($accessable)) $diskname = $accessable;
        
        $disk = self::getMediaDisk((string) $diskname);

        return collect($disk ? $disk->default_access : config('filesystems.disk_default_access'));
    }

    public static function computeAccessVia($accessable): Collection
    {
        
        if ($accessable instanceof Media) return self::computeAccessViaModel($accessable);
        
        if (is_string($accessable))
        {
            $model = self::firstWhere('src_path', $accessable);
            return $model ? self::computeAccessViaModel($model) : self::defaultAccess($accessable);
        }
        
        return self::defaultAccess($accessable);
    }
    // END: Access control



    public static function getDirectory(string $path): ?Media
    {
        return self::where('src_path', $path)->where('mime_type', 'directory')->first();
    }



    public function getCdnPathAttribute(): string
    {
        return url(route('media', $this->src_path));
    }



    public function setOwner($model)
    {
        $this->owner()->associate($model);
    }



    public static function discover(string $path)
    {
        $path = self::dissectPath($path);
        $parent = self::getDirectory($path->path);
        $disk = self::getMediaDiskOrFail($path->diskname); // Keep in; the "OrFail" part is important

        // When subdirectory exists: check if the parent media model exists
        if ($path->hasSubdirectory && !$parent) throw new \Exception('The parent directory does not exist.');


        // Get all files and directories in the path
        foreach (Storage::files($path->path) as $file)
        {
            // Check if the file already exists
            if (!self::where('src_path', $file)->exists()) self::createMediaFromPath($file);
        }

        foreach (Storage::directories($path->path) as $directory)
        {
            // Check if the directory already exists
            if (!self::where('src_path', $directory)->exists()) self::createMediaFromPath($directory);

            // Recursively discover the directory
            self::discover($directory);
        }
    }



    public static function createMediaFromPath(string $path): Media
    {
        $path = self::dissectPath($path);
        $parent = self::getDirectory($path->filepath);
        $filename = self::dissectFilename($path->filename);
        
        $mime = Storage::mimeType($path->path) ?: 'directory';
        $isFile = $mime !== 'directory';
        
        $extension = $isFile ? $filename->extension : null;
        $size = $isFile ? Storage::size($path->path) : null;

        // Mime types to check for cases where the extension shoud determine the mime type
        if (in_array($mime, ['text/plain', 'text/x-c', 'application/x-empty']))
        {
            switch ($extension)
            {
                case 'js': $mime = 'text/javascript'; break;
                case 'md': $mime = 'text/markdown'; break;
                case 'csv': $mime = 'text/csv'; break;
                case 'css': $mime = 'text/css'; break;
                case 'xml': $mime = 'text/xml'; break;
                case 'txt': $mime = 'text/plain'; break;
                case 'html': $mime = 'text/html'; break;
            }
        }



        $media = self::updateOrCreate([
            'src_path' => $path->path,
        ],[
            'parent_id' => optional($parent)->id,
            'drive' => $path->diskname,
            'mime_type' => $mime,
            'name' => $path->filename,
            'meta' => [
                'extension' => $extension,
                'size' => $size,
            ],
        ]);

        $media->generateThumbnail();

        return $media;
    }



    public static function upload(string $path, UploadedFile|SymfonyUploadedFile $file, string $name = null): Media
    {
        $path = self::dissectPath($path);
        $disk = self::getMediaDiskOrFail($path->diskname);
        $parent = self::getDirectory($path->path);

        if ($path->hasSubdirectory)
        {
            if (!$disk->allow_subdirectory_creation) throw new \Exception('The disk "' . $path->diskname . '" does not allow subdirectory creation.');
            if (!$parent) throw new \Exception('The parent directory does not exist.');
        }

        // Generate hashname
        $hashname = (new File($file))->hashName();

        // If disk doesn't allow for custom names, set $name to hashname
        if (!$disk->allow_custom_filename) $name = $hashname;

        // If no name is provided, set $name to original clientname or hashname
        $name = $name ?? $file->getClientOriginalName() ?? $hashname;


        $storagePath = Storage::putFileAs($path->path, $file, $name);
        $media = self::createMediaFromPath($storagePath);
        $user = auth()->user();
        
        if ($user) $media->setOwner($user);
        
        return $media->fresh();
    }



    // TODO: Make this async
    // TODO: Add ImageMagick
    // TODO: Add SVG support
    // TODO: Add FFMPEG
    // TODO: Add Video thumbnail support
    // TODO: Add Audio thumbnail support
    public function generateThumbnail(): void
    {
        $thumbnail = match ($this->mime_type)
        {
            'image/jpeg' => self::rasterImageToThumbnail($this->src_path),
            'image/png' => self::rasterImageToThumbnail($this->src_path),
            'image/gif' => self::rasterImageToThumbnail($this->src_path),
            default => null,
        };

        $this->update([ 'thumbnail' => $thumbnail ]);
    }

    // START: Media conversions
    public static function rasterImageToThumbnail(string $path): ?string
    {
        return Image::read(Storage::get($path))
        ->scaleDown(300, 300, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->toWebp(quality: 50)
        ->toDataUri();
    }
    // END: Media conversions



    public static function createDirectory(string $path, string $name): Media
    {
        $path = self::dissectPath($path);
        $disk = self::getMediaDiskOrFail($path->diskname);
        $parent = self::getDirectory($path->path);

        if (!$disk->allow_subdirectory_creation) throw new \Exception('The disk "' . $path->diskname . '" does not allow subdirectory creation.');
        if ($path->hasSubdirectory && !$parent) throw new \Exception('The parent directory does not exist.');

        $storagePath = $path->path . '/' . $name;        

        Storage::makeDirectory($storagePath);
        $media = self::createMediaFromPath($storagePath);
        $user = auth()->user();
        
        if ($user) $media->setOwner($user);

        return $media->fresh();
    }



    public function rename(string $newName): void
    {
        $this->move($this->dissectPath($this->src_path)->filepath, $newName);
    }



    public function move(string $destinationPath, string $filename = null): void
    {
        self::moveMany([$this], $destinationPath, $filename);
    }

    public static function moveMany(array $items, string $destinationPath, string $filename = null): void
    {
        $destinationPath = self::dissectPath($destinationPath);
        $filename = $filename ? self::dissectFilename($filename) : null;

        $disk = self::getMediaDiskOrFail($destinationPath->diskname);
        $parent = self::getDirectory($destinationPath->path);

        if ($destinationPath->hasSubdirectory && !$parent) throw new \Exception('The parent directory does not exist.');

        // Loop through all items
        foreach ($items as $index => $item)
        {
            // Get model directly or by path
            $media = $item instanceof Media ? $item : self::findPathOrFail($item);

            // Parse old path
            $oldPath = self::dissectPath($media->src_path);

            // Create the new filename (either enumerated custom filename or original filename)
            $newFilename = $oldPath->filename;

            if ($filename)
            {
                $extension = $media->mime_type !== 'directory' ? '.'.$filename->extension : '';
                $index = $index ? "_$index" : '';
                $newFilename = $filename->name.$index.$extension;
            }

            // Parse new path
            $newPath = self::dissectPath($destinationPath->path . '/' . $newFilename);

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
            $media->update([
                'parent_id' => optional($parent)->id,
                'drive' => $newPath->diskname,
                'src_path' => $newPath->path,
                'name' => $newPath->filename,
            ]);

            // Update the path of all children
            if ($media->mime_type == 'directory')
            {
                $media->recursivePathUpdate($oldPath->path, $newPath->path);
            }
        }
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



    public function copy(string $destinationPath, string $name = null): Void
    {
        self::copyMany([$this], $destinationPath, $name);
    }

    public static function copyMany(array $items, string $destinationPath, string $filename = null): void
    {
        $destinationPath = self::dissectPath($destinationPath);
        $filename = $filename ? self::dissectFilename($filename) : null;

        $disk = self::getMediaDiskOrFail($destinationPath->diskname);
        $parent = self::getDirectory($destinationPath->path);

        if ($destinationPath->hasSubdirectory && !$parent) throw new \Exception('The parent directory does not exist.');

        // Loop through all items
        foreach ($items as $index => $item)
        {
            // Get model directly or by path
            $media = $item instanceof Media ? $item : self::findPathOrFail($item);

            // Parse old path
            $oldPath = self::dissectPath($media->src_path);

            // Create the new filename (either enumerated custom filename or original filename)
            $newFilename = $filename ? ($filename->name.($index ? "_$index" : '').'.'.$filename->extension) : $oldPath->filename;

            // Parse new path
            $newPath = self::dissectPath($destinationPath->path . '/' . $newFilename);

            // Check if the new path already exists
            if (Storage::exists($newPath->path))
            {
                throw new \Exception('The media already exists in destination.');
            }

            // Try copying the file on disk
            if (!Storage::copy($oldPath->path, $newPath->path))
            {
                throw new \Exception('The media could not be copied to destination.');
            }

            // Copy the media model recursively
            $media->recursiveModelCopy($parent, $newPath->path);
        }
    }

    private function recursiveModelCopy(?Media $parent, string $newPath): Model
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
            $child->recursiveModelCopy($this, $newPath->path . '/' . $child->name);
        }

        // Return the new model
        return $newModel;
    }



    public static function deleteMany(array $paths): void
    {
        Media::whereIn('src_path', $paths)->get()->each(fn ($media) => $media->delete());
    }
}
