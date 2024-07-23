<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Collection;

class RolePolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, Role $model): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_ROLES)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function view(User $user, Role $role): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_ROLES)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function create(User $user, array $permissions): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_CREATE_ROLES])) return Response::deny('You are missing the required permission.');

        // If creation includes forbidden permissions
        if (Permissions::partOfForbidden($permissions)) return Response::deny('Some permissions are not allowed to be added.');

        // If creation includes elevated permissions
        if (Permissions::partOfElevated($permissions) && !$user->is_admin) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }
    
    
    
    public function update(User $user, Role $role, array $newPermissions): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_EDIT_ROLES])) return Response::deny('You are missing the required permission.');

        $currentPermissions = $role->permissions()->pluck('name')->toArray();
        $removedPermissions = array_diff($currentPermissions, $newPermissions);
        $addedPermissions = array_diff($newPermissions, $currentPermissions);

        // If edit includes forbidden permissions
        if (Permissions::partOfForbidden($removedPermissions)) return Response::deny('Some permissions are not allowed to be removed.');
        if (Permissions::partOfForbidden($addedPermissions)) return Response::deny('Some permissions are not allowed to be added.');

        // If edit includes elevated permissions
        if (Permissions::partOfElevated($removedPermissions) && !$user->is_admin) return Response::deny('You are missing the required permission.');
        if (Permissions::partOfElevated($addedPermissions) && !$user->is_admin) return Response::deny('You are missing the required permission.');
        
        return Response::allow();
    }

    
    
    public function delete(User $user, Role $role): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_DELETE_ROLES])) return Response::deny('You are missing the required permission.');

        // If role is administrative user needs admin permission
        if ($role->is_administrative && !$user->can(Permissions::SYSTEM_ADMIN)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function deleteMany(User $user, Collection $roles): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_DELETE_ROLES])) return Response::deny('You are missing the required permission.');
        
        foreach ($roles as $role)
        {
            // If role is administrative user needs admin permission
            if ($role->is_administrative && !$user->can(Permissions::SYSTEM_ADMIN)) return Response::deny('You are missing the required permission.');
        }

        return Response::allow();
    }

    
    
    public function restore(User $user, Role $role): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, Role $role): Response
    {
        return Response::deny();
    }
}
