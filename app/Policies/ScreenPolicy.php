<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;

class ScreenPolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, Screen $screen): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_SCREENS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    

    public function view(User $user, Screen $screen): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_SCREENS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function create(User $user): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENS, Permissions::APP_CREATE_SCREENS])) return Response::deny('You are missing the required permission.');
        
        return Response::allow();
    }



    public function update(User $user, Screen $screen): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENS, Permissions::APP_EDIT_SCREENS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function delete(User $user, Screen $screen): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENS, Permissions::APP_DELETE_SCREENS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function deleteMany(User $user, Collection $screens): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENS, Permissions::APP_DELETE_SCREENS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function restore(User $user, Screen $screen): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, Screen $screen): Response
    {
        return Response::deny();
    }
}
