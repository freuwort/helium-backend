<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'content',
        'background',
        'width',
        'height',
        'duration',
    ];

    protected $casts = [
        'content' => 'array',
    ];



    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->owner()->associate(auth()->user())->save();
        });
    }



    // START: Relationships
    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(ScreenPlaylist::class);
    }
    // END: Relationships
}
