<?php

namespace App\Models;

use App\Traits\HasAccessControl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentPostGroup extends Model
{
    use HasFactory, HasAccessControl;

    public $timestamps = false;

    protected $fillable = [
        'space_id',
        'post_id',
        'owner_id',
        'hidden',
    ];

    protected $casts = [
        'hidden' => 'boolean',
    ];



    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->owner()->associate(auth()->user())->save();
        });
    }



    // START: Relationships
    public function space()
    {
        return $this->belongsTo(ContentSpace::class, 'space_id');
    }

    public function post()
    {
        return $this->belongsTo(ContentPost::class, 'post_id');
    }

    public function posts()
    {
        return $this->hasMany(ContentPost::class, 'group_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
    // END: Relationships
}
