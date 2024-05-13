<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class MediaAccess extends Model
{
    public $timestamps = false;
    
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
