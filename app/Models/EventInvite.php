<?php

namespace App\Models;

use App\Http\Resources\Event\TemplateEventInviteResource;
use App\Traits\HasAccessControl;
use App\Traits\NotifiableViaTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EventInvite extends Model
{
    use HasFactory, HasAccessControl, Notifiable, NotifiableViaTemplate;

    protected $templateResourceClass = TemplateEventInviteResource::class;
    
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



    // START: Attributes
    public function getInviteLinkAttribute()
    {
        return route('event-invite.assign', $this->code);
    }
    // END: Attributes
}
