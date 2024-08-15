<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenPlaylist extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'type',
        'name',
        'screen_order',
    ];

    protected $casts = [
        'screen_order' => 'array',
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

    public function devices()
    {
        return $this->belongsToMany(ScreenDevice::class);
    }

    public function screens()
    {
        return $this->hasMany(Screen::class)->withPivot(['from_date', 'from_time', 'to_date', 'to_time', 'on_days']);
    }
    // END: Relationships
}
