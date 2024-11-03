<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'user_info';

    protected $fillable = [
        'name',
        'salutation',
        'prefix',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'nickname',
        'legalname',
        'organisation',
        'department',
        'job_title',
        'customer_id',
        'employee_id',
        'member_id',
        'notes',
    ];



    protected static function booted(): void
    {
        static::saving(function ($model) {
            $model->updateName();
        });
    }



    // START: Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function main_address()
    {
        return $this->belongsTo(Address::class, 'main_address_id');
    }

    public function billing_address()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shipping_address()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
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



    public function updateName($name = null)
    {
        $this->name = $name ?: $this->fullname_or_nickname;
        $this->saveQuietly();
    }
}
