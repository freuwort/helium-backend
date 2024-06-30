<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    // TODO: Check if additional permission checks are needed
    public function basicViewAny(User $user): bool
    {
        return true;
    }



    // TODO: Check if additional permission checks are needed
    public function basicView(User $user, Category $model): bool
    {
        return true;
    }



    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::APP_VIEW_CONTENT);
    }

    

    public function view(User $user, Category $model): bool
    {
        return $user->can(Permissions::APP_VIEW_CONTENT);
    }



    public function create(User $user): bool
    {
        return $user->can([Permissions::APP_VIEW_CONTENT, Permissions::APP_CREATE_CONTENTCATEGORIES]);
    }



    public function update(User $user, Category $model): bool
    {
        return $user->can([Permissions::APP_VIEW_CONTENT, Permissions::APP_EDIT_CONTENTCATEGORIES]);
    }



    public function delete(User $user, Category $model): bool
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_CONTENT, Permissions::APP_DELETE_CONTENTCATEGORIES])) return false;

        return true;
    }

    
    
    public function deleteMany(User $user, $ids): bool
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_CONTENT, Permissions::APP_DELETE_CONTENTCATEGORIES])) return false;
        
        // Check every user
        foreach (Category::whereIn('id', $ids)->get() as $db_event)
        {
        }

        return true;
    }

    
    
    public function restore(User $user, Category $model): bool
    {
        return false;
    }

    
    
    public function forceDelete(User $user, Category $model): bool
    {
        return false;
    }
}
