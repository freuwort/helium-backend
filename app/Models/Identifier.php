<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Identifier extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'type',
        'label',
        'value',
    ];



    public function identifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
