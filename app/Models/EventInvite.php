<?php

namespace App\Models;

use App\Http\Resources\Event\TemplateEventInviteResource;
use App\Traits\HasAccessControl;
use App\Traits\HasForm;
use App\Traits\NotifiableViaTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EventInvite extends Model
{
    use HasFactory, HasAccessControl, Notifiable, NotifiableViaTemplate, HasForm;

    protected $templateResourceClass = TemplateEventInviteResource::class;
    
    protected $fillable = [
        'event_id',
        'user_id',
        'form_id',
        'type',
        'needs_claim',
        'name',
        'email',
        'phone',
        'code',
        'status',
    ];

    protected $casts = [
        'needs_claim' => 'boolean',
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

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
    // END: Relationships



    // START: Attributes
    public function getInviteLinkAttribute()
    {
        return 'https://example.com/event-invite/'.$this->code;
    }
    // END: Attributes
}
