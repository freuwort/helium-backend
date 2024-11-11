<?php

namespace App\Models;


use App\Classes\Permissions\Permissions;
use App\Notifications\ResetPasswordNotification;
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
        'username',
        'email',
        'phone',
        'password',
        'requires_password_change',
        'requires_two_factor',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'enabled_at',
        'blocked_at',
        'block_reason',
        'deleted_at',

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'enabled_at' => 'datetime',
        'deleted_at' => 'datetime',
        'password' => 'hashed',
        'requires_password_change' => 'boolean',
        'requires_two_factor' => 'boolean',
    ];

    protected $with = [
        'two_factor_methods',
    ];

    public $media_types = [
        'avatar',
        'banner',
    ];



    // START: Boot events
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->updateName();
        });
    }
    // END: Boot events



    // START: Relationships
    public function two_factor_methods()
    {
        return $this->morphMany(TwoFactorMethod::class, 'authenticatable');
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

    public function raw_settings()
    {
        return $this->hasMany(UserSetting::class);
    }
    // END: Relationships



    // START: Scopes
    public function scopeWhereIsAdmin($query) {
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

    public function scopeWhereHasElevatedPermissions($query) {
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

    public function scopeWhereCan($query, string $permission) {
        return $query->where(function ($query) use ($permission) {
            $query->where(function ($query) use ($permission){
                $query->whereHas('permissions', function ($query) use ($permission) {
                    $query->where('name', $permission);
                })
                ->orWhereHas('roles', function ($query) use ($permission) {
                    $query->whereHas('permissions', function ($query) use ($permission) {
                        $query->where('name', $permission);
                    });
                });
            })
            ->orWhere(function ($query) {
                $query->whereIsAdmin();
            });
        });
    }

    public function scopeWhereEmailVerified($query, $value = true) {
        if ($value) return $query->whereNotNull('email_verified_at');
        return $query->whereNull('email_verified_at');
    }

    public function scopeWherePhoneVerified($query, $value = true) {
        if ($value) return $query->whereNotNull('phone_verified_at');
        return $query->whereNull('phone_verified_at');
    }

    public function scopeWhereEnabled($query, $value = true) {
        if ($value) return $query->whereNotNull('enabled_at');
        return $query->whereNull('enabled_at');
    }

    public function scopeWhereBlocked($query, $value = true) {
        if ($value) return $query->whereNotNull('blocked_at');
        return $query->whereNull('blocked_at');
    }

    public function scopeWhereTfaEnabled($query, $value = true) {
        if ($value) return $query->whereHas('two_factor_methods', function ($query) {
            return $query->where('enabled', true);
        });

        return $query->whereDoesntHave('two_factor_methods', function ($query) {
            return $query->where('enabled', true);
        });
    }
    // END: Scopes



    // START: Attributes
    public function getFullnameAttribute()
    {
        return implode(' ', array_filter([$this->prefix, $this->firstname, $this->middlename, $this->lastname, $this->suffix]));
    }

    public function getFullnameOrNicknameAttribute()
    {
        return $this->fullname ? $this->fullname : $this->nickname;
    }

    public function getSettingsAttribute() {
        return $this->raw_settings->mapWithKeys(fn ($item) => [$item['key'] => $item['value']]);
    }

    public function getIsAdminAttribute(): bool {
        return Permissions::partOfAdmin($this->getAllPermissionNames());
    }

    public function getHasForbiddenPermissionsAttribute(): bool {
        return Permissions::partOfForbidden($this->getAllPermissionNames());
    }

    public function getHasElevatedPermissionsAttribute(): bool {
        return Permissions::partOfElevated($this->getAllPermissionNames());
    }

    public function getHasTfaEnabledAttribute(): bool {
        return $this->two_factor_methods->filter(fn ($e) => $e->enabled)->isNotEmpty();
    }
    // END: Attributes



    /**
     * Get the default profile media.
     * 
     * @param  string  $type
     * @return string
     */
    public function getDefaultProfileMedia(string $type): string
    {
        if ($type == 'avatar') return url('/default/profile_'.((crc32($this->email) % 8) + 1).'.jpg');
        if ($type == 'banner') return url('/default/banner_'.((crc32($this->email) % 8) + 1).'.jpg');

        return url('/default/banner_1.jpg');
    }

    /**
     * Update the user's name.
     *
     * @param  string  $name
     * @return void
     */
    public function updateName(string $name = null): void
    {
        $this->name = $name ?: $this->fullname_or_nickname;
        $this->saveQuietly();
    }

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
     * Verify the user's phone.
     * 
     * @param  bool  $verified
     * @return void
     */
    public function verifyPhone(bool $verified = true): void
    {
        $this->update([
            'phone_verified_at' => $verified ? now() : null
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

    /**
     * Block the user.
     *
     * @param  bool  $blocked
     * @param  string|null  $reason
     * @return void
     */
    public function block(bool $blocked = true, string $reason = null): void
    {
        $this->update([
            'blocked_at' => $blocked ? now() : null,
            'block_reason' => $blocked ? $reason : null,
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



    public function wantsNotificationsFor($type, $via = 'mail'): bool|null
    {
        return $this->getSetting('notification_'.$via.'_'.$type);
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



    public function sendPasswordResetNotification($token)
    {
        $url = config('app.frontend_url')."/auth/reset-password?token=$token&email=$this->email";
        $this->notify(new ResetPasswordNotification($this, $url));
    }
}
