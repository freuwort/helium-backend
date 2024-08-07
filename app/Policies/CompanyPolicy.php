<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;

class CompanyPolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, Company $company): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_COMPANIES)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function view(User $user, Company $company): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_VIEW_COMPANIES)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function create(User $user): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_COMPANIES, Permissions::SYSTEM_CREATE_COMPANIES])) return Response::deny('You are missing the required permission.');
        
        return Response::allow();
    }

    
    
    public function update(User $user, Company $company): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_COMPANIES, Permissions::SYSTEM_EDIT_COMPANIES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function delete(User $user, Company $company): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_COMPANIES, Permissions::SYSTEM_DELETE_COMPANIES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function deleteMany(User $user, Collection $companies): Response
    {
        // Permission check
        if (!$user->can([Permissions::SYSTEM_VIEW_COMPANIES, Permissions::SYSTEM_DELETE_COMPANIES])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function restore(User $user, Company $company): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, Company $company): Response
    {
        return Response::deny();
    }
}
