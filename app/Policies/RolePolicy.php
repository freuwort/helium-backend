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



    public function basicView(User $user, Role $role): Response
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

        // Check if user tries to assign roles they don't have themselves
        if (!$user->can($permissions)) return Response::deny('You can only assign permissions you have yourself.');

        return Response::allow();
    }



    public function import(User $user, Collection|array $roles): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_CREATE_ROLES])) return Response::deny('You are missing the required permission.');

        foreach ($roles as $role)
        {
            // If creation includes forbidden permissions
            if (Permissions::partOfForbidden($role['permissions'])) return Response::deny('Some permissions are not allowed to be added.');

            // If creation includes elevated permissions
            if (Permissions::partOfElevated($role['permissions']) && !$user->is_admin) return Response::deny('You are missing the required permission.');

            // Check if user tries to assign roles they don't have themselves
            if (!$user->can($role['permissions'])) return Response::deny('You can only assign permissions you have yourself.');
        }

        return Response::allow();
    }
    
    
    
    public function update(User $user, Role $role, array $newPermissions): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_EDIT_ROLES])) return Response::deny('You are missing the required permission.');

        $currentPermissions = $role->getPermissionNames()->toArray();
        $removedPermissions = array_diff($currentPermissions, $newPermissions);
        $addedPermissions = array_diff($newPermissions, $currentPermissions);
        $changedPermissions = array_merge($removedPermissions, $addedPermissions);

        // If edit includes forbidden permissions
        if (Permissions::partOfForbidden($changedPermissions)) return Response::deny('Some permissions are not allowed to be added or removed.');

        // If edit includes elevated permissions
        if (Permissions::partOfElevated($changedPermissions) && !$user->is_admin) return Response::deny('You are missing the required permission.');

        // Check if user tries to assign roles they don't have themselves
        if (!$user->can($changedPermissions)) return Response::deny('You can only assign permissions you have yourself.');
        
        return Response::allow();
    }



    public function assignMany(User $user, Collection $roles): Response
    {
        if (!$user->can([Permissions::SYSTEM_ASSIGN_ROLES])) return Response::deny('You are missing the required permission.');

        foreach ($roles as $role)
        {
            $permissions = $role->getPermissionNames();

            if (Permissions::partOfForbidden($permissions)) return Response::deny('Some roles are not allowed to be assigned or removed.');
            if (Permissions::partOfElevated($permissions) && !$user->is_admin) return Response::deny('You are missing the required permission.');
            if (!$user->can($permissions)) return Response::deny('You can only assign permissions you have yourself.');
        }

        return Response::allow();
    }

    
    
    public function delete(User $user, Role $role): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_DELETE_ROLES])) return Response::deny('You are missing the required permission.');

        // If deletion includes forbidden permissions
        if (Permissions::partOfForbidden($role->getPermissionNames())) return Response::deny('This role is not allowed to be removed.');

        // If deletion includes elevated permissions
        if (Permissions::partOfElevated($role->getPermissionNames()) && !$user->is_admin) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function deleteMany(User $user, Collection $roles): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_ROLES, Permissions::SYSTEM_DELETE_ROLES])) return Response::deny('You are missing the required permission.');
        
        foreach ($roles as $role)
        {
            // If deletion includes forbidden permissions
            if (Permissions::partOfForbidden($role->getPermissionNames())) return Response::deny('Some roles are not allowed to be removed.');

            // If deletion includes elevated permissions
            if (Permissions::partOfElevated($role->getPermissionNames()) && !$user->is_admin) return Response::deny('You are missing the required permission.');
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
