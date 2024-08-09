<?php

namespace App\Models;

use App\Classes\Utils;
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
use Spatie\PdfToImage\Pdf;

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
    public function generateThumbnail(): void
    {
        $thumbnail = match ($this->mime_type)
        {
            // Image types
            'image/jpeg' => self::rasterImageToThumbnail($this->src_path),
            'image/png' => self::rasterImageToThumbnail($this->src_path),
            'image/gif' => self::rasterImageToThumbnail($this->src_path),
            'image/bmp' => self::rasterImageToThumbnail($this->src_path),
            'image/webp' => self::rasterImageToThumbnail($this->src_path),
            'image/tiff' => self::rasterImageToThumbnail($this->src_path),

            // Vector types
            'image/svg+xml' => self::vectorToThumbnail($this->src_path),

            // Audio types
            'audio/mpeg' => self::audioToThumbnail($this->src_path),

            // Video types
            'video/mp4' => self::videoToThumbnail($this->src_path),
            'video/webm' => self::videoToThumbnail($this->src_path),
            'video/ogg' => self::videoToThumbnail($this->src_path),
            'video/quicktime' => self::videoToThumbnail($this->src_path),
            
            // Other types
            'application/pdf' => self::pdfToThumbnail($this->src_path),

            // Fallback
            default => null,
        };

        $this->update([ 'thumbnail' => $thumbnail ]);
    }

    // START: Media conversions
    public static function rasterImageToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");

        return Image::read($input)
        ->pad(300, 300, '#00000000', 'center')
        ->toWebp(quality: 50)
        ->toDataUri();
    }

    public static function vectorToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");

        return Image::read($input)
        ->pad(300, 300, '#00000000', 'center')
        ->toWebp(quality: 50)
        ->toDataUri();
    }

    public static function audioToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");
        $shell_input = escapeshellarg($input);

        $hash = hash_file('sha256', $input);

        $output = storage_path("app/temp/$hash.wav");
        $shell_output = escapeshellarg($output);

        shell_exec( "ffmpeg -y -i $shell_input -vn -acodec pcm_s16le -ar 8000 -ac 1 $shell_output");



        $amplitudes = [];

        $file = fopen($output, 'rb');

        while (!feof($file))
        {
            $bytes = fread($file, 2);
        
            // Break if no bytes are left
            if (strlen($bytes) < 2) break;
            
            // Unpack bytes into integer
            $amplitude = unpack('s', $bytes)[1];
            
            // Add amplitude to array
            $amplitudes[] = $amplitude;
        }

        fclose($file);

        // Remove temp file
        unlink($output);



        $sampleSize = 300;

        // Normalize amplitudes
        $amplitudes = array_map(fn ($amplitude) => min(abs($amplitude), 32768) / 32768 * 3, $amplitudes);

        // Combine amplitudes into chunks
        $amplitudes = array_chunk($amplitudes, ceil(count($amplitudes) / $sampleSize));

        // Average chunks
        $amplitudes = array_map(fn ($chunk) => min(round((int) array_sum($chunk) / count($chunk) * 100), 100), $amplitudes);
        


        $width = 300;
        $height = 300;
        $colors = Utils::interpolateColors('#ff00ff', '#f59e0b', $sampleSize);
        $image = Image::read(public_path('default/thumbnail_background_audio.png'))
        ->pad($width, $height, '#00000000', 'center');
        
        foreach ($amplitudes as $key => $amplitude)
        {
            $amplitude = min(($amplitude + 2), $height);
            $x = $key * $width / $sampleSize;
            $y1 = $height / 2 - $amplitude / 2;
            $y2 = $height / 2 + $amplitude / 2;
            $color = $colors[$key];

            $image->drawLine(function ($line) use ($x, $y1, $y2, $color) {
                $line->from($x, $y1);
                $line->to($x, $y2);
                $line->color($color);
                $line->width(1);
            });
        }

        return $image
        ->toWebp(quality: 50)
        ->toDataUri();
    }

    public static function videoToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");
        $shell_input = escapeshellarg($input);

        $hash = hash_file('sha256', $input);

        $output = storage_path("app/temp/$hash.jpg");
        $shell_output = escapeshellarg($output);

        shell_exec("ffmpeg -y -i $shell_input -ss 00:00:01.000 -update 1 -vframes 1 $shell_output");

        $image = Image::read($output)
        ->pad(300, 300, '#00000000', 'center')
        ->place(public_path('default/thumbnail_foreground_video.png'), 'center', 0, 0, 100)
        ->toWebp(quality: 50)
        ->toDataUri();

        // Remove temp file
        unlink($output);

        return $image;
    }

    public static function pdfToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");
        $shell_input = escapeshellarg($input);

        $hash = hash_file('sha256', $input);

        $output = storage_path("app/temp/$hash");
        $shell_output = escapeshellarg($output);
        $suffix = '-000001.png';

        shell_exec("pdftopng -f 1 -l 1 -q $shell_input $shell_output");

        $image = Image::read($output.$suffix)
        ->pad(300, 300, '#00000000', 'center')
        ->toWebp(quality: 50)
        ->toDataUri();

        // Remove temp file
        unlink($output.$suffix);

        return $image;
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
