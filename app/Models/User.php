<?php

namespace App\Models;


use App\Classes\Permissions\Permissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    public function raw_settings()
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
    public function getProfileImageAttribute()
    {
        return 'https://avatar.iran.liara.run/public/?username=' . $this->username . '&size=72';
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



    // Sync a model with the user
    public function syncModel(string $model, array $data, string $model_name = null): void
    {
        // Get the model class
        $model_class = new $model;

        // Get the model name
        $model_name = $model_name ?? strtolower(class_basename($model_class));

        // Get the model's relationship name
        $model_relationship_name = Str::plural($model_name);

        // Get the model's relationship
        $model_relationship = $this->$model_relationship_name();

        // Get the model's relationship IDs
        $model_relationship_ids = $model_relationship->pluck('id');

        // Get the model's relationship IDs that are not in the data
        $unused_model_relationship_ids = $model_relationship_ids->diff(collect($data)->pluck('id'));

        // Delete the unused model relationship IDs
        $model_relationship->whereIn('id', $unused_model_relationship_ids)->delete();

        // Loop through the data
        foreach ($data as $item)
        {
            // Update or create the model
            $model_relationship->updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
