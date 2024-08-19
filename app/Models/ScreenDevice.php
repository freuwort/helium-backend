<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Pivots\DevicePlaylistPivot;

class ScreenDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'group',
        'secret',
    ];

    protected $hidden = [
        'secret',
    ];

    protected $with = [
        'owner',
        'address',
    ];



    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->owner()->associate(auth()->user())->save();
            $model->generateSecret();
        });
    }



    // START: Relationships
    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function playlists()
    {
        return $this->belongsToMany(ScreenPlaylist::class)->using(DevicePlaylistPivot::class)->withPivot(['from_date', 'from_time', 'to_date', 'to_time', 'on_days', 'on_screen']);
    }
    // END: Relationships



    // START: Methods
    public function generateSecret()
    {
        $this->update(['secret' => bin2hex(random_bytes(64))]);
    }
    // END: Methods
}
