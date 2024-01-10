<?php

namespace App\Models;


use App\Classes\Permissions\Permissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'ident_number',
        'email',
        'password',
        'email_verified_at',
        'enabled_at',
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'enabled_at' => 'datetime',
        'deleted_at' => 'datetime',
        'password' => 'hashed',
    ];



    // START: Relationships
    public function user_name()
    {
        return $this->hasOne(UserName::class);
    }

    public function user_company()
    {
        return $this->hasOne(UserCompany::class);
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function bank_connections(): MorphMany
    {
        return $this->morphMany(BankConnection::class, 'bankable');
    }

    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'emailable');
    }

    public function phonenumbers(): MorphMany
    {
        return $this->morphMany(Phonenumber::class, 'phoneable');
    }

    public function dates(): MorphMany
    {
        return $this->morphMany(Date::class, 'dateable');
    }

    public function links(): MorphMany
    {
        return $this->morphMany(Link::class, 'linkable');
    }
    // END: Relationships



    // START: Attributes
    public function get_profile_image_attribute()
    {
        return '/images/app/default/user.png';
    }

    public function get_setting_values_attribute()
    {
        $settings = $this->settings->mapWithKeys(function ($item) {
            return [$item['key'] => $item['value']];
        });

        return $settings;
    }

    public function get_is_admin_attribute()
    {
        return $this->canAny([Permissions::SYSTEM_SUPER_ADMIN, Permissions::SYSTEM_ADMIN]);
    }

    public function get_is_super_admin_attribute()
    {
        return $this->can(Permissions::SYSTEM_SUPER_ADMIN);
    }
    // END: Attributes



    // START: Settings
    public function hasSetting($key)
    {
        return $this->settings()->where('key', $key)->exists();
    }

    public function setSetting(string|array $key, $value = null): void
    {
        // If the key is an array
        if (is_array($key))
        {
            // Loop through the array and then set each key-value pair
            foreach ($key as $k => $v)
            {
                $this->setSetting($k, $v);
            }

            return;
        }

        // Set the key-value pair
        $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function unsetSetting($key)
    {
        $this->settings()->where('key', $key)->delete();
    }

    public function getSetting($key)
    {
        if (!$this->hasSetting($key)) return null;
        return $this->settings()->firstWhere('key', $key)->value;
    }
    // END: Settings
}
