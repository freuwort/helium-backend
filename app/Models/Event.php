<?php

namespace App\Models;

use App\Traits\HasAccessControl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasAccessControl;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'inherit_access',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];



    // START: Relationships
    public function parent()
    {
        return $this->belongsTo(Event::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Event::class, 'parent_id');
    }

    public function invites()
    {
        return $this->hasMany(EventInvite::class);
    }
    // END: Relationships
}
