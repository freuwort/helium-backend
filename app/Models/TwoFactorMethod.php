<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'recipient',
        'secret',
        'code',
        'default',
        'enabled',
    ];

    protected $casts = [
        'default' => 'boolean',
        'enabled' => 'boolean',
    ];

    protected $hidden = [
        'secret',
        'code',
    ];



    public function authenticatable()
    {
        return $this->morphTo();
    }
}
