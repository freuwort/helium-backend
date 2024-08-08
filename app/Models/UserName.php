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



    protected static function booted(): void
    {
        static::saving(function ($model) {
            $model->updateParentName();
        });
    }



    // START: Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // END: Relationships



    // START: Attributes
    public function getFullnameAttribute()
    {
        return implode(' ', array_filter([$this->prefix, $this->firstname, $this->middlename, $this->lastname, $this->suffix]));
    }

    public function getFullnameOrNicknameAttribute()
    {
        return $this->fullname ? $this->fullname : $this->nickname;
    }
    // END: Attributes



    // START: Update parent name
    public function updateParentName()
    {
        $this->user()->update([
            'name' => $this->fullname_or_nickname,
        ]);
    }
    // END: Update parent name
}
