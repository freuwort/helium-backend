<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Collection;

class UserPolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, User $model): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_USERS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    

    public function view(User $user, User $model): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_USERS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function create(User $user): Response
    {
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_CREATE_USERS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function import(User $user, Collection|array $models): Response
    {
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_CREATE_USERS])) return Response::deny('You are missing the required permission.');

        foreach ($models as $model)
        {
            if (!$model['roles']) continue;
            if ($model['roles']->isEmpty()) continue;

            if (!$user->can(Permissions::SYSTEM_ASSIGN_ROLES)) return Response::deny('You are missing the required permission.');

            $roles = Role::whereIn('name', $model['roles'])->get();
            
            foreach ($roles as $role)
            {
                $permissions = $role->getPermissionNames();
                if (Permissions::partOfForbidden($permissions)) return Response::deny('Some roles are not allowed to be assigned.');
                if (Permissions::partOfElevated($permissions) && !$user->is_admin) return Response::deny('You are missing the required permission.');
                if (!$user->can($permissions)) return Response::deny('You can only assign permissions you have yourself.');
            }
        }

        return Response::allow();
    }



    public function update(User $user, User $model): Response
    {
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_EDIT_USERS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function sendVerificationEmail(User $user, User $model): Response
    {
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS]) || !$user->canAny([Permissions::SYSTEM_CREATE_USERS, Permissions::SYSTEM_EDIT_USERS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function adminAction(User $user, User $model): Response
    {
        // Permission check
        if (!$user->canAny([Permissions::ADMIN_PERMISSIONS])) return Response::deny('You are missing the required permission.');

        // Check if edit includes forbidden permissions
        if ($model->has_forbidden_permissions) return Response::deny('Action cannot be performed on this user.');
        
        return Response::allow();
    }



    public function enable(User $user, User $model): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_ENABLE_USERS])) return Response::deny('You are missing the required permission.');

        // Check if edit includes forbidden permissions
        if ($model->has_forbidden_permissions) return Response::deny('User status cannot be manually changed.');

        // Check if edit includes elevated permissions
        if ($model->has_elevated_permissions && !$user->is_admin) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function uploadAvatar(User $user, User $model): Response
    {
        // Check if domain policy allows profile avatar upload (only for the user themself)
        if (Setting::getSetting('policy_allow_avatar_upload', false) && $user->id == $model->id) return Response::allow();

        // Permission check
        if ($user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_EDIT_USERS])) return Response::allow();
        
        return Response::deny('You are missing the required permission.');
    }

    

    public function uploadBanner(User $user, User $model): Response
    {
        // Check if domain policy allows profile banner upload (only for the user themself)
        if (Setting::getSetting('policy_allow_banner_upload', false) && $user->id == $model->id) return Response::allow();

        // Permission check
        if ($user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_EDIT_USERS])) return Response::allow();
        
        return Response::deny('You are missing the required permission.');
    }



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
