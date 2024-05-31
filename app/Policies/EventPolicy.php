<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    // TODO: Check if additional permission checks are needed
    public function basicViewAny(User $user): bool
    {
        return true;
    }



    // TODO: Check if additional permission checks are needed
    public function basicView(User $user, Event $model): bool
    {
        return true;
    }



    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::APP_VIEW_EVENTS);
    }

    

    public function view(User $user, Event $model): bool
    {
        return $user->can(Permissions::APP_VIEW_EVENTS);
    }



    public function create(User $user): bool
    {
        return $user->can([Permissions::APP_VIEW_EVENTS, Permissions::APP_CREATE_EVENTS]);
    }



    public function update(User $user, Event $model): bool
    {
        return $user->can([Permissions::APP_VIEW_EVENTS, Permissions::APP_EDIT_EVENTS]);
    }



    public function delete(User $user, Event $model): bool
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_EVENTS, Permissions::APP_DELETE_EVENTS])) return false;

        return true;
    }

    
    
    public function deleteMany(User $user, $ids): bool
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_EVENTS, Permissions::APP_DELETE_EVENTS])) return false;
        
        // Check every user
        foreach (Event::whereIn('id', $ids)->get() as $db_event)
        {
        }

        return true;
    }

    
    
    public function restore(User $user, Event $model): bool
    {
        return false;
    }

    
    
    public function forceDelete(User $user, Event $model): bool
    {
        return false;
    }
}
