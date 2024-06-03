<?php

namespace App\Models;

use App\Traits\HasAccessControl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventInvite extends Model
{
    use HasFactory, HasAccessControl;

    protected $fillable = [
        'event_id',
        'user_id',
        'email',
        'phone',
        'code',
        'status',
    ];



    // START: Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // END: Relationships
}
