<?php

namespace App\Models;


use App\Classes\Permissions\Permissions;
use App\Traits\HasMedia;
use App\Traits\HasTwoFactorAuthentication;
use App\Traits\SyncMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles, HasApiTokens, HasFactory, HasTwoFactorAuthentication, HasMedia, Notifiable, SyncMany, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'requires_password_change',
        'requires_two_factor',
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
        'requires_password_change' => 'boolean',
        'requires_two_factor' => 'boolean',
    ];

    protected $with = [
        'user_name',
        'user_company',
        'two_factor_methods',
    ];

    public $media_types = [
        'avatar',
        'banner',
    ];



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

    public function scopeWhereCan($query, $permissions)
    {
        return $query
        ->where(function ($query) use ($permissions) {
            return $query
            ->whereHas('permissions', function ($query) use ($permissions) {
                return $query
                ->whereIn('name', [...$permissions, ...Permissions::ADMIN_PERMISSIONS]);
            })
            ->orWhereHas('roles', function ($query) use ($permissions) {
                return $query
                ->whereHas('permissions', function ($query) use ($permissions) {
                    return $query
                    ->whereIn('name', [...$permissions, ...Permissions::ADMIN_PERMISSIONS]);
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
    public function getSettingsAttribute()
    {
        return $this->raw_settings->mapWithKeys(function ($item) {
            return [$item['key'] => $item['value']];
        });
    }

    public function getIsAdminAttribute(): bool
    {
        return Permissions::partOfAdmin($this->getAllPermissionNames());
    }

    public function getHasForbiddenPermissionsAttribute(): bool
    {
        return Permissions::partOfForbidden($this->getAllPermissionNames());
    }

    public function getHasElevatedPermissionsAttribute(): bool
    {
        return Permissions::partOfElevated($this->getAllPermissionNames());
    }

    public function getHasTfaEnabledAttribute(): bool
    {
        return $this->two_factor_methods->filter(fn ($e) => $e->enabled)->isNotEmpty();
    }
    // END: Attributes



    // START: Profile media
    public function getDefaultProfileMedia($type)
    {
        return url(route('default.image', [$type, $this->username ?? 'Unknown']));
    }
    // END: Profile media



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



    public function wantsNotificationsFor($type, $via = 'email'): bool
    {
        return $this->getSetting('notification_'.$via.'_'.$type) ?? false;
    }
    // END: Settings



    // START: Misc methods
    private function getAllPermissionNames()
    {
        $rolePermissions = $this->roles->pluck('permissions')->flatten()->pluck('name');
        $directPermissions = $this->permissions->pluck('name');

        return $rolePermissions->merge($directPermissions)->unique();
    }
    // END: Misc methods
}
