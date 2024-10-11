<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateManyUserRolesRequest;
use App\Models\Role;

class UserRoleController extends Controller
{
    public function assignRoles(UpdateManyUserRolesRequest $request)
    {
        $this->authorize('assignMany', [Role::class, $request->roles]);
        
        $roleIds = $request->roles->pluck('id')->toArray();
        foreach ($request->items as $user) $user->roles()->syncWithoutDetaching($roleIds);
    }

    public function revokeRoles(UpdateManyUserRolesRequest $request)
    {
        $this->authorize('assignMany', [Role::class, $request->roles]);

        $roleIds = $request->roles->pluck('id')->toArray();
        foreach ($request->items as $user) $user->roles()->detach($roleIds);
    }
}
