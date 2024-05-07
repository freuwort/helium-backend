<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    // TODO: Check if additional permission checks are needed
    public function basicViewAny(User $user): bool
    {
        return true;
    }



    // TODO: Check if additional permission checks are needed
    public function basicView(User $user, Role $model): bool
    {
        return true;
    }



    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::SYSTEM_VIEW_ROLES);
    }

    
    
    public function view(User $user, Role $role): bool
    {
        return $user->can(Permissions::SYSTEM_VIEW_ROLES);
    }

    
    
    public function create(User $user): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_CREATE_ROLES]);
    }
    
    
    
    public function update(User $user, Role $role): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_EDIT_ROLES]);
    }

    
    
    public function delete(User $user, Role $role): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_DELETE_ROLES]);
    }



    public function deleteMany(User $user): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_DELETE_ROLES]);
    }

    
    
    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    
    
    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
