<?php

namespace App\Models;

use App\Traits\HasAccessControl;
use App\Traits\SyncMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasAccessControl, SyncMany;

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

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }
    // END: Relationships



    // START: Attributes
    public function getMainAddressAttribute()
    {
        return $this->addresses()->where('type', 'main')->first();
    }

    public function getBillingAddressAttribute()
    {
        return $this->addresses()->where('type', 'billing')->first();
    }

    public function getShippingAddressAttribute()
    {
        return $this->addresses()->where('type', 'shipping')->first();
    }

    public function getIsLiveAttribute()
    {
        return $this->start_at <= now() && $this->end_at >= now();
    }

    public function getIsPastAttribute()
    {
        return $this->end_at < now();
    }

    public function getIsFutureAttribute()
    {
        return $this->start_at > now();
    }
    // END: Attributes
}
