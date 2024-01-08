<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Phonenumber extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'type',
        'number',
        'verified_at',
    ];



    public function phoneable(): MorphTo
    {
        return $this->morphTo();
    }
}
