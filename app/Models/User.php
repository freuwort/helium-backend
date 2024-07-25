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
    public function two_factor_methods()
    {
        return $this->morphMany(TwoFactorMethod::class, 'authenticatable');
    }

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



    // START: Scopes
    public function scopeWhereIsAdmin($query)
    {
        return $query
        ->where(function ($query) {
            return $query
            ->whereHas('permissions', function ($query) {
                return $query
                ->whereIn('name', Permissions::ADMIN_PERMISSIONS);
            })
            ->orWhereHas('roles', function ($query) {
                return $query
                ->whereHas('permissions', function ($query) {
                    return $query
                    ->whereIn('name', Permissions::ADMIN_PERMISSIONS);
                });
            });
        });
    }

    public function scopeWhereHasElevatedPermissions($query)
    {
        return $query
        ->where(function ($query) {
            return $query
            ->whereHas('permissions', function ($query) {
                return $query
                ->whereIn('name', Permissions::ELEVATED_PERMISSIONS);
            })
            ->orWhereHas('roles', function ($query) {
                return $query
                ->whereHas('permissions', function ($query) {
                    return $query
                    ->whereIn('name', Permissions::ELEVATED_PERMISSIONS);
                });
            });
        });
    }

    public function scopeWhereEmailVerified($query, $value = true)
    {
        if ($value) return $query->whereNotNull('email_verified_at');
        return $query->whereNull('email_verified_at');
    }

    public function scopeWhereEnabled($query, $value = true)
    {
        if ($value) return $query->whereNotNull('enabled_at');
        return $query->whereNull('enabled_at');
    }

    public function scopeWhereTfaEnabled($query, $value = true)
    {
        if ($value) return $query->whereHas('two_factor_methods', function ($query) {
            return $query->where('enabled', true);
        });

        return $query->whereDoesntHave('two_factor_methods', function ($query) {
            return $query->where('enabled', true);
        });
    }
    // END: Scopes



    // START: Attributes
    public function getProfileImageAttribute()
    {
        $media = $this->media()->where('type', 'profile_image')->first();

        return $media ? $media->cdn_path : url(route('default.image', ['profile_image', $this->username]));
    }

    public function getProfileBannerAttribute()
    {
        $media = $this->media()->where('type', 'profile_banner')->first();
        
        return $media ? $media->cdn_path : url(route('default.image', ['profile_banner', $this->username]));
    }

    public function getSettingsAttribute()
    {
        return $this->raw_settings->mapWithKeys(function ($item) {
            return [$item['key'] => $item['value']];
        });
    }

    public function getIsAdminAttribute()
    {
        return Permissions::partOfAdmin($this->getAllPermissions()->pluck('name')->toArray());
    }

    public function getHasForbiddenPermissionsAttribute()
    {
        return Permissions::partOfForbidden($this->getAllPermissions()->pluck('name')->toArray());
    }

    public function getHasElevatedPermissionsAttribute()
    {
        return Permissions::partOfElevated($this->getAllPermissions()->pluck('name')->toArray());
    }

    public function getHasTfaEnabledAttribute()
    {
        return $this->two_factor_methods()->where('enabled', true)->exists();
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



    /**
     * Verify the user's email.
     *
     * @param  bool  $verified
     * @return void
     */
    public function verifyEmail(bool $verified = true): void
    {
        $this->update([
            'email_verified_at' => $verified ? now() : null
        ]);
    }



    /**
     * Enable the user.
     *
     * @param  bool  $enabled
     * @return void
     */
    public function enable(bool $enabled = true): void
    {
        $this->update([
            'enabled_at' => $enabled ? now() : null
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
