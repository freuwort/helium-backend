<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Event;
use App\Models\ContentSpace;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContentSpacePolicy
{
    // TODO: Check if additional permission checks are needed
    public function basicViewAny(User $user): bool
    {
        return true;
    }



    // TODO: Check if additional permission checks are needed
    public function basicView(User $user, ContentSpace $model): bool
    {
        return true;
    }



    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::APP_VIEW_CONTENT);
    }

    

    public function view(User $user, ContentSpace $model): bool
    {
        return $user->can(Permissions::APP_VIEW_CONTENT);
    }



    public function create(User $user): bool
    {
        return $user->can([Permissions::APP_VIEW_CONTENT, Permissions::APP_CREATE_CONTENTSPACES]);
    }



    public function update(User $user, ContentSpace $model): bool
    {
        return $user->can([Permissions::APP_VIEW_CONTENT, Permissions::APP_EDIT_CONTENTSPACES]);
    }



    public function delete(User $user, ContentSpace $model): bool
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_CONTENT, Permissions::APP_DELETE_CONTENTSPACES])) return false;

        return true;
    }

    
    
    public function deleteMany(User $user, $ids): bool
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_CONTENT, Permissions::APP_DELETE_CONTENTSPACES])) return false;
        
        // Check every user
        foreach (ContentSpace::whereIn('id', $ids)->get() as $db_event)
        {
        }

        return true;
    }

    
    
    public function restore(User $user, ContentSpace $model): bool
    {
        return false;
    }

    
    
    public function forceDelete(User $user, ContentSpace $model): bool
    {
        return false;
    }
}
