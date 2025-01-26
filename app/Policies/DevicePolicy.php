<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Device;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;

class DevicePolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, Device $device): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_DEVICES)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    

    public function view(User $user, Device $device): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_DEVICES)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function create(User $user): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_DEVICES, Permissions::SYSTEM_CREATE_DEVICES])) return Response::deny('You are missing the required permission.');
        
        return Response::allow();
    }



    public function update(User $user, Device $device): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_DEVICES, Permissions::SYSTEM_EDIT_DEVICES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function delete(User $user, Device $device): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_DEVICES, Permissions::SYSTEM_DELETE_DEVICES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function deleteMany(User $user, Collection $devices): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_DEVICES, Permissions::SYSTEM_DELETE_DEVICES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function restore(User $user, Device $device): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, Device $device): Response
    {
        return Response::deny();
    }
}
