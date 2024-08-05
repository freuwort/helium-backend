<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Media;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Collection;

class MediaPolicy
{
    // TODO: implement
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }


    // TODO: implement
    public function basicView(User $user, Media $model): Response
    {
        return Response::allow();
    }


    // TODO: implement
    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_USERS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    // TODO: implement
    public function view(User $user, User $model): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_USERS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function upload(User $user, string $path): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can upload files to this path
        if (!Media::checkIfUser(['src_path', $path], $user, ['write', 'admin'])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }


    // TODO: implement
    public function update(User $user, User $model, Collection $newRoles): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_EDIT_USERS])) return Response::deny('You are missing the required permission.');

        $currentRoles = $model->roles;
        $removedRoles = $currentRoles->diff($newRoles);
        $addedRoles = $newRoles->diff($currentRoles);
        $changedRoles = $removedRoles->merge($addedRoles);

        // Check if user tries to assign roles
        if ($changedRoles->isNotEmpty())
        {
            // Check if user can generally assign roles
            if (!$user->can(Permissions::SYSTEM_ASSIGN_ROLES)) return Response::deny('You are missing the required permission.');
            
            // Check if specific roles can be assigned
            foreach ($changedRoles as $role)
            {
                // If edit includes forbidden permissions
                if (Permissions::partOfForbidden($role->getPermissionNames())) return Response::deny('Some roles are not allowed to be assigned or removed.');

                // If edit includes elevated permissions
                if (Permissions::partOfElevated($role->getPermissionNames()) && !$user->is_admin) return Response::deny('You are missing the required permission.');

                // Check if user tries to assign roles with permissions they don't have themselves
                if (!$user->can($role->getPermissionNames())) return Response::deny('You can only assign permissions you have yourself.');
            }
        }

        return Response::allow();
    }


    // TODO: implement
    public function delete(User $user, User $model): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_DELETE_USERS])) return Response::deny('You are missing the required permission.');

        // Check if user tries to delete themself
        if ($model->id == $user->id) return Response::deny('You cannot delete yourself.');

        // Check if deletion includes forbidden permissions
        if ($model->has_forbidden_permissions) return Response::deny('User cannot be deleted.');

        // Check if deletion includes elevated permissions
        if ($model->has_elevated_permissions && !$user->is_admin) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    // TODO: implement
    public function deleteMany(User $user, Collection $models): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_DELETE_USERS])) return Response::deny('You are missing the required permission.');
        
        // Check specific users
        foreach ($models as $model)
        {
            // Check if user tries to delete themself
            if ($model->id == $user->id) return Response::deny('You cannot delete yourself.');

            //  Check if deletion includes forbidden permissions
            if ($model->has_forbidden_permissions) return Response::deny('User cannot be deleted.');

            // Check if deletion includes elevated permissions
            if ($model->has_elevated_permissions && !$user->is_admin) return Response::deny('You are missing the required permission.');
        }

        return Response::allow();
    }

    
    
    public function restore(User $user, User $model): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, User $model): Response
    {
        return Response::deny();
    }
}
