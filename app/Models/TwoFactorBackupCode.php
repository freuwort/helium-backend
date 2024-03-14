<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorBackupCode extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'code',
    ];

    protected $hidden = [
        'code',
    ];



    public function authenticatable()
    {
        return $this->morphTo();
    }
}
