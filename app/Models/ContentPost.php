<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'owner_id',
        'type',
        'name',
        'slug',
        'excerpt',
        'content',
        'review_ready',
        'approved_at',
    ];

    protected $casts = [
        'review_ready' => 'boolean',
        'approved_at' => 'datetime',
    ];



    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->owner()->associate(auth()->user())->save();
        });
    }



    // START: Relationships
    public function group()
    {
        return $this->belongsTo(ContentPostGroup::class, 'group_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
    // END: Relationships
}
