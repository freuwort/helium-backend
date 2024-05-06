<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    // TODO: Check if additional permission checks are needed
    public function basicViewAny(User $user): bool
    {
        return true;
    }



    // TODO: Check if additional permission checks are needed
    public function basicView(User $user, User $model): bool
    {
        return true;
    }



    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::SYSTEM_VIEW_USERS);
    }

    

    public function view(User $user, User $model): bool
    {
        return $user->can(Permissions::SYSTEM_VIEW_USERS);
    }



    public function create(User $user): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_CREATE_USERS]);
    }



    public function update(User $user, User $model): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_EDIT_USERS]);
    }



    public function delete(User $user, User $model): bool
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_DELETE_USERS])) return false;

        // Check if user tries to delete themself
        if ($model->id == $user->id) return false;

        // Check if user tries to delete a model with higher permissions
        if ($model->hasHigherPermissionsThan($user)) return false;

        return true;
    }

    
    
    public function deleteMany(User $user, $ids): bool
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_USERS, Permissions::SYSTEM_DELETE_USERS])) return false;
        
        // Check every user
        foreach (User::whereIn('id', $ids)->get() as $db_user)
        {
            // Check if user tries to delete themself
            if ($db_user->id == $user->id) return false;

            // Check if user tries to delete a model with higher permissions
            if ($db_user->hasHigherPermissionsThan($user)) return false;
        }

        return true;
    }

    
    
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    
    
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
