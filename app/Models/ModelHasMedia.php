<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Traits\HasRoles;



class ModelHasMedia extends Model
{
    protected $fillable = [
        'media_id',
        'model_id',
        'model_type',
        'type',
        'permission',
    ];



    // START: Relationships
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'model_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'model_id');
    }
    // END: Relationships
}
