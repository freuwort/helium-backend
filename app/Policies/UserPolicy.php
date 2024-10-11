<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
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
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_CREATE_USERS])) return Response::deny('You are missing the required permission.');

        foreach ($models as $model)
        {
            // Check if user tries to assign roles
            if ($model['roles'] && $model['roles']->isNotEmpty())
            {
                // Check if user can generally assign roles
                if (!$user->can(Permissions::SYSTEM_ASSIGN_ROLES)) return Response::deny('You are missing the required permission.');
                
                // Check if specific roles can be assigned
                foreach ($model['roles'] as $roleName)
                {
                    $role = $user->roles()->where('name', $roleName)->first();

                    // If creation includes forbidden permissions
                    if (Permissions::partOfForbidden($role->getPermissionNames())) return Response::deny('Some roles are not allowed to be assigned.');

                    // If creation includes elevated permissions
                    if (Permissions::partOfElevated($role->getPermissionNames()) && !$user->is_admin) return Response::deny('You are missing the required permission.');

                    // Check if user tries to assign roles with permissions they don't have themselves
                    if (!$user->can($role->getPermissionNames())) return Response::deny('You can only assign permissions you have yourself.');
                }
            }
        }

        return Response::allow();
    }



    public function update(User $user, User $model): Response
    {
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_EDIT_USERS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function updateUsername(User $user, User $model): Response
    {
        // Check if domain policy allows username change (only for the user themself)
        if (Setting::getSetting('policy_allow_username_change', false) && $user->id == $model->id) return Response::allow();

        // Permission check
        if ($user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_EDIT_USERS])) return Response::allow();
        
        return Response::allow();
    }



    public function updatePassword(User $user, User $model)
    {
        return $this->checkAdministrativeAction($user, $model);
    }

    public function requirePasswordChange(User $user, User $model)
    {
        return $this->checkAdministrativeAction($user, $model);
    }

    public function requireTwoFactor(User $user, User $model)
    {
        return $this->checkAdministrativeAction($user, $model);
    }

    public function verifyEmail(User $user, User $model)
    {
        return $this->checkAdministrativeAction($user, $model);
    }

    private function checkAdministrativeAction(User $user, User $model): Response
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
