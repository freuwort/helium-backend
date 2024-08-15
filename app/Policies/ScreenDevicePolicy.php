<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\ScreenDevice;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;

class ScreenDevicePolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, ScreenDevice $device): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_SCREENDEVICES)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    

    public function view(User $user, ScreenDevice $device): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_SCREENDEVICES)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function create(User $user): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENDEVICES, Permissions::APP_MANAGE_SCREENDEVICES])) return Response::deny('You are missing the required permission.');
        
        return Response::allow();
    }



    public function update(User $user, ScreenDevice $device): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENDEVICES, Permissions::APP_MANAGE_SCREENDEVICES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function delete(User $user, ScreenDevice $device): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENDEVICES, Permissions::APP_MANAGE_SCREENDEVICES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function deleteMany(User $user, Collection $devices): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENDEVICES, Permissions::APP_MANAGE_SCREENDEVICES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function restore(User $user, ScreenDevice $device): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, ScreenDevice $device): Response
    {
        return Response::deny();
    }
}
