<?php

namespace App\Models;

use App\Classes\Drives\Drives;
use App\Jobs\Media\GenerateThumbnail;
use App\Permissions\Permissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;

//////////////////////////////////
// THIS MODEL NEEDS A REFACTOR! //
//////////////////////////////////

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



    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            // Queue thumbnail generation
            GenerateThumbnail::dispatch($model);
        });

        self::deleting(function ($model) {
            // Check if the model is a folder
            if (!Storage::mimeType($model->path)) Storage::deleteDirectory($model->path);
            // Otherwise, delete the file from disk
            else Storage::delete($model->path);

            // And delete the thumbnail if it exists
            if ($model->thumbnail_path) Storage::delete($model->thumbnail_path);
        });
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



    public static function getDriveRoot($drive)
    {
        return Media::where('parent_id', null)->where('mediatype', 'folder')->where('drive', $drive)->first() ?? null;
    }



    public function getRoot()
    {
        $model = $this;

        while ($model->parent)
        {
            $model = $model->parent;
        }

        return $model;
    }



    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path) return null;

        return '/storage/media/thumbnails/'.$this->id;
    }



    public function getDirectPermissionConfigAttribute()
    {
        return [
            'mode' => $this->permission_mode,
            'users' => [],
            'roles' => [],
            'profiles' => $this->profiles->pluck('profile')->toArray(),
        ];
    }

    public function getCalculatedPermissionConfigAttribute()
    {
        $model = $this;

        while ($model->parent && $model->permission_mode == 'inherit')
        {
            $model = $model->parent;
        }

        return $model->getDirectPermissionConfigAttribute();
    }



    public function getUrlAttribute()
    {
        $rootMedia = $this->getRoot();

        if ($rootMedia && $rootMedia->drive == 'private') return '/storage/private/'.$this->id;

        return Storage::url($this->path);
    }



    public function move($old_path, $new_path)
    {
        function updatePathRecursive(Media $media, $old_path, $new_path)
        {
            // Replace only the start of the path
            $media->update(['path' => $new_path . substr($media->path, strlen($old_path))]);
    
            foreach ($media->children as $child)
            {
                updatePathRecursive($child, $old_path, $new_path);
            }
        }



        Storage::move($old_path, $new_path);

        updatePathRecursive($this, $old_path, $new_path);
    }



    public function canAccess(Request $request)
    {
        // Get root media
        $rootMedia = $this->getRoot();

        // Get drive
        $drive = $rootMedia ? Drives::getDrive($rootMedia->drive) : null;



        // If the media is in a drive that doesn't exist, it can't be accessed
        if (!$drive) return false;
        
        // If the media is in a public drive, everyone can access it
        if ($drive['private'] === false) return true;



        // Get permission config
        $permissionConfig = $this->calculatedPermissionConfig;

        // If the media is public, everyone can access it
        if ($permissionConfig['mode'] == 'public') return true;



        // Get user
        $user = $request->user();

        // If the request is unauthenticated, they can't access the media
        if (!$user) return false;
        
        // If the user has admin privileges, they can access all media
        if ($user->can([Permissions::SYSTEM_SUPER_ADMIN, Permissions::SYSTEM_ADMIN])) return true;



        // If the media has a custom permission config
        if ($permissionConfig['mode'] == 'custom')
        {
            // If the user is in the users list, they can access the media
            if (in_array($user->id, $permissionConfig['users'])) return true;
    
            // If the user has any of the roles in the roles list, they can access the media
            if ($user->hasAnyRole($permissionConfig['roles'])) return true;
    
            // If the user has any of the profiles in the profiles list, they can access the media
            if ($user->hasAnyProfile($permissionConfig['profiles'])) return true;
        }



        // Otherwise, they can't access the media
        return false;
    }



    public function generateThumbnail()
    {
        // All thumbnails are saved in app/thumbnails as [uuid].png files
        // Intervention image is used to generate the thumbnails
        // The height and or width of the thumbnail is 300px, while maintaining the aspect ratio

        // Get the file mime type
        $mime = Storage::mimeType($this->src_path);

        // Prepare path
        $path = null;

        // Execute the correct function based on the mime type
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'])) $path = $this->imageToImage();
        else if (in_array($mime, ['video/mp4', 'video/quicktime'])) $path = $this->videoToImage();
        else if (in_array($mime, ['application/pdf'])) $path = $this->pdfToImage();

        // If the path is null, return
        if (!$path) return;

        // Update the thumbnail path
        $this->update(['thumbnail' => $path]);
    }

    private function imageToImage()
    {
        $maxWidth = 250;
        $maxHeight = 250;
        $uuid = Str::uuid();
        $path = 'thumbnails/'.$uuid.'.png';

        ini_set("memory_limit", "512M");

        // Get the image
        $image = \Intervention\Image\Facades\Image::make(Storage::path($this->path));

        // Cancel the longer side
        ($image->height() < $image->width()) ? $maxWidth = null : $maxHeight = null;
        
        // Resize the image
        $image->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Save the image
        $image->save(Storage::path($path));

        return $path;
    }

    private function videoToImage()
    {
        return null;
    }

    private function pdfToImage()
    {
        $maxWidth = 250;
        $uuid = Str::uuid();
        $path = 'thumbnails/'.$uuid.'.png';

        $pdf = new \Spatie\PdfToImage\Pdf(Storage::path($this->path));
        
        $pdf
        ->width($maxWidth)
        ->saveImage(Storage::path($path));

        return $path;
    }
}
