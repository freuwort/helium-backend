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
        'backup_codes',
        'default',
        'enabled',
    ];

    protected $casts = [
        'backup_codes' => 'array',
        'default' => 'boolean',
        'enabled' => 'boolean',
    ];

    protected $hidden = [
        'secret',
        'code',
        'backup_codes',
    ];



    public function authenticatable()
    {
        return $this->morphTo();
    }
}
