<?php

namespace App\Models;

use App\Classes\Permissions\Permissions;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'color',
        'icon',
        'guard_name',
    ];

    public static $defaultColor = '#363E40';
    public static $defaultIcon = 'category';



    // START: Relationships
    public function accesses()
    {
        return $this->morphMany(Access::class, 'permissible');
    }
    // END: Relationships



    // START: Scopes
    public function scopeWhereIsAdmin($query)
    {
        return $query
        ->whereHas('permissions', function ($query) {
            return $query->whereIn('name', Permissions::ADMIN_PERMISSIONS);
        });
    }

    public function scopeWhereHasElevatedPermissions($query)
    {
        return $query
        ->whereHas('permissions', function ($query) {
            return $query->whereIn('name', Permissions::ELEVATED_PERMISSIONS);
        });
    }
    // END: Scopes



    // START: Attributes
    public function getColorAttribute()
    {
        return $this->attributes['color'] ?? self::$defaultColor;
    }

    public function getIconAttribute()
    {
        return $this->attributes['icon'] ?? self::$defaultIcon;
    }

    public function getIsAdminAttribute()
    {
        return Permissions::partOfAdmin($this->permissions()->pluck('name')->toArray());
    }

    public function getHasForbiddenPermissionsAttribute()
    {
        return Permissions::partOfForbidden($this->permissions()->pluck('name')->toArray());
    }

    public function getHasElevatedPermissionsAttribute()
    {
        return Permissions::partOfElevated($this->permissions()->pluck('name')->toArray());
    }
    // END: Attributes
}
