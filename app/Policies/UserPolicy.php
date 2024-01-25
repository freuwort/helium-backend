<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::SYSTEM_VIEW_USERS);
    }

    

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->can(Permissions::SYSTEM_VIEW_USERS);
    }



    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_CREATE_USERS]);
    }



    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_EDIT_USERS]);
    }



    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_DELETE_USERS])) return false;

        // Check if user tries to delete themself
        if ($model->id == $user->id) return false;

        // Check if user tries to delete a user with higher permissions
        if ($model->hasHigherPermissionsThan($user)) return false;

        return true;
    }

    /**
     * Determine whether the user can delete a list of models.
     */
    public function deleteMany(User $user, $ids): bool
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_DELETE_USERS])) return false;
        
        // Check every user
        foreach (User::whereIn('id', $ids)->get() as $db_user)
        {
            // Check if user tries to delete themself
            if ($db_user->id == $user->id) return false;

            // Check if user tries to delete a user with higher permissions
            if ($db_user->hasHigherPermissionsThan($user)) return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
