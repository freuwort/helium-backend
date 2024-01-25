<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserName extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'salutation',
        'prefix',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'nickname',
        'legalname',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function getFullnameAttribute()
    {
        return implode(' ', array_filter([$this->prefix, $this->firstname, $this->middlename, $this->lastname, $this->suffix]));
    }

    public function getFullnameOrNicknameAttribute()
    {
        return $this->fullname ? $this->fullname : $this->nickname;
    }
}
