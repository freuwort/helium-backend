<?php

namespace App\Models;

use App\Traits\SyncMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Company extends Model
{
    use HasRoles, HasFactory, SyncMany, SoftDeletes;

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



    // START: Relationships
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



    // START: Attributes
    public function getProfileImageAttribute()
    {
        return 'https://avatar.iran.liara.run/public/?username=' . $this->name . '&size=72';
    }
    // END: Attributes



    // START: Specific Addresses
    public function get_legal_address_attribute()
    {
        return $this->addresses()->where('type', 'legal')->first();
    }

    public function get_billing_address_attribute()
    {
        return $this->addresses()->where('type', 'billing')->first();
    }
    // END: Specific Addresses



    // START: Duplicate
    public function duplicate()
    {
        $model = $this->replicate();
        $model->push();

        $model->addresses()->saveMany($this->addresses()->get()->map(function ($item) { return $item->replicate(); }));
        $model->legal_details()->saveMany($this->legal_details()->get()->map(function ($item) { return $item->replicate(); }));
        $model->bank_details()->saveMany($this->bank_details()->get()->map(function ($item) { return $item->replicate(); }));
        $model->emails()->saveMany($this->emails()->get()->map(function ($item) { return $item->replicate(); }));
        $model->phonenumbers()->saveMany($this->phonenumbers()->get()->map(function ($item) { return $item->replicate(); }));
        $model->significant_dates()->saveMany($this->significant_dates()->get()->map(function ($item) { return $item->replicate(); }));
        $model->website_links()->saveMany($this->website_links()->get()->map(function ($item) { return $item->replicate(); }));

        return $model;
    }
    // END: Duplicate
}
