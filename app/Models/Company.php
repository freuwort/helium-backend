<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\SyncMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Permission\Traits\HasRoles;

class Company extends Model
{
    use HasRoles, HasFactory, SyncMany, HasMedia;

    protected $fillable = [
        'name',
        'legal_form',
        'description',
        'notes',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public $media_types = [
        'logo',
        'banner',
    ];



    // START: Relationships
    public function identifiers()
    {
        return $this->morphMany(Identifier::class, 'identifiable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function legal_details(): MorphMany
    {
        return $this->morphMany(LegalDetail::class, 'governable');
    }

    public function bank_connections(): MorphMany
    {
        return $this->morphMany(BankConnection::class, 'bankable');
    }

    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'emailable');
    }

    public function phonenumbers(): MorphMany
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function dates(): MorphMany
    {
        return $this->morphMany(Date::class, 'dateable');
    }

    public function links(): MorphMany
    {
        return $this->morphMany(Link::class, 'linkable');
    }
    // END: Relationships



    // START: Profile media
    public function getDefaultProfileMedia($type)
    {
        return url(route('default.image', [$type, $this->name ?? 'Unknown']));
    }
    // END: Profile media



    // START: Specific Addresses
    public function getLegalAddressAttribute()
    {
        return $this->addresses()->where('type', 'legal')->first();
    }

    public function getBillingAddressAttribute()
    {
        return $this->addresses()->where('type', 'billing')->first();
    }
    // END: Specific Addresses
}
