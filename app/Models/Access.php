<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Access extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'permissible_id',
        'permissible_type',
        'type',
        'permission',
    ];



    // START: Relationships
    public function accessable()
    {
        return $this->morphTo();
    }

    public function permissible()
    {
        return $this->morphTo();
    }
    // END: Relationships
}
