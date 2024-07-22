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

    const DEFAULT_COLOR = '#363E40';
    const DEFAULT_ICON = 'category';



    // START: Scopes
    public function scopeWhereIsAdministrative($query)
    {
        return $query
        ->whereHas('permissions', function ($query) {
            return $query
            ->where('name', Permissions::SYSTEM_ADMIN)
            ->orWhere('name', Permissions::SYSTEM_SUPER_ADMIN);
        });
    }
    // END: Scopes



    // START: Attributes
    public function getColorAttribute()
    {
        return $this->attributes['color'] ?? self::DEFAULT_COLOR;
    }

    public function getIconAttribute()
    {
        return $this->attributes['icon'] ?? self::DEFAULT_ICON;
    }

    public function getIsAdministrativeAttribute()
    {
        return $this->hasPermissionTo(Permissions::SYSTEM_SUPER_ADMIN) || $this->hasPermissionTo(Permissions::SYSTEM_ADMIN);
    }
    // END: Attributes
}
