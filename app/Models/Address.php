<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country_code',
        'latitude',
        'longitude',
        'notes',
    ];



    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }



    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            implode(' ', array_filter([$this->address_line_1, $this->address_line_2])),
            implode(' ', array_filter([$this->postal_code, $this->city])),
        ]));
    }


    public function getFullAddressWithCountryAttribute(): string
    {
        return implode(', ', array_filter([
            implode(' ', array_filter([$this->address_line_1, $this->address_line_2])),
            implode(' ', array_filter([$this->postal_code, $this->city])),
            implode(' ', array_filter([$this->state, optional($this->country)->name])),
        ]));
    }
}
