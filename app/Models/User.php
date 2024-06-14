<?php

namespace App\Models;


use App\Classes\Permissions\Permissions;
use App\Traits\HasTwoFactorAuthentication;
use App\Traits\SyncMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles, HasApiTokens, HasFactory, HasTwoFactorAuthentication, Notifiable, SyncMany, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
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

    public const MEDIA_IMAGE = 'profile_image';
    public const MEDIA_BANNER = 'profile_banner';



    // START: Relationships
    public function user_name()
    {
        return $this->hasOne(UserName::class);
    }

    public function user_company()
    {
        return $this->hasOne(UserCompany::class);
    }

    public function raw_settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function identifiers()
    {
        return $this->morphMany(Identifier::class, 'identifiable');
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

    public function media()
    {
        return $this->morphToMany(Media::class, 'model', 'model_has_media')->withPivot('type');
    }
    // END: Relationships



    // START: Attributes
    public function getProfileImageAttribute()
    {
        $media = $this->media()->where('type', 'profile_image')->first();

        if ($media) return $media->cdn_path;
        
        return "https://api.dicebear.com/7.x/identicon/svg?seed=".$this->username."&scale=65&size=72&backgroundColor=eeeeee";
    }

    public function getProfileBannerAttribute()
    {
        $media = $this->media()->where('type', 'profile_banner')->first();

        if ($media) return $media->cdn_path;
        
        return null;
    }

    public function getSettingsAttribute()
    {
        return $this->raw_settings->mapWithKeys(function ($item) {
            return [$item['key'] => $item['value']];
        });
    }

    public function getIsAdminAttribute()
    {
        return $this->canAny([Permissions::SYSTEM_SUPER_ADMIN, Permissions::SYSTEM_ADMIN]);
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->can(Permissions::SYSTEM_SUPER_ADMIN);
    }
    // END: Attributes



    /**
     * Update the user's password. The password is hashed automatically.
     *
     * @param  array  $password
     * @return void
     */
    public function updatePassword(string $password): void
    {
        $this->update([
            'password' => Hash::make($password),
        ]);
    }



    // START: Settings
    public function hasSetting($key)
    {
        return $this->raw_settings()->where('key', $key)->exists();
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
        $this->raw_settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function unsetSetting($key)
    {
        $this->raw_settings()->where('key', $key)->delete();
    }

    public function getSetting($key)
    {
        if (!$this->hasSetting($key)) return null;
        return $this->raw_settings()->firstWhere('key', $key)->value;
    }
    // END: Settings



    // START: Permissions
    public function hasHigherPermissionsThan(User $user)
    {
        // If the user is a super admin and the other user is not
        if ($this->can(Permissions::SYSTEM_SUPER_ADMIN) && !$user->can(Permissions::SYSTEM_SUPER_ADMIN)) return true;

        // If the user is an admin and the other user is not
        if ($this->can(Permissions::SYSTEM_ADMIN) && !$user->can(Permissions::SYSTEM_ADMIN)) return true;

        return false;
    }
    // END: Permissions



    // START: Profile media
    public function uploadProfileMedia(UploadedFile $file, String $type)
    {
        if(!in_array($type, ['profile_image', 'profile_banner'])) return;

        // Delete old profile media
        $this->media()->where('type', $type)->get()->each(function ($media) {
            $media->delete();
        });        

        // Upload new profile media
        $media = Media::upload('profiles', $file);

        // Set profile image
        $this->media()->syncWithoutDetaching([[
            'media_id' => $media->id,
            'type' => $type,
        ]]);
    }
    // END: Profile media
}
