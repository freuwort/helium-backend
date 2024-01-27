<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::SYSTEM_VIEW_COMPANIES);
    }

    
    
    public function view(User $user, Company $company): bool
    {
        return $user->can(Permissions::SYSTEM_VIEW_COMPANIES);
    }

    
    
    public function create(User $user): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_COMPANIES, Permissions::SYSTEM_CREATE_COMPANIES]);
    }

    
    
    public function update(User $user, Company $company): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_COMPANIES, Permissions::SYSTEM_EDIT_COMPANIES]);
    }

    
    
    public function delete(User $user, Company $company): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_COMPANIES, Permissions::SYSTEM_DELETE_COMPANIES]);
    }

    
    
    public function deleteMany(User $user): bool
    {
        return $user->can([Permissions::SYSTEM_VIEW_COMPANIES, Permissions::SYSTEM_DELETE_COMPANIES]);
    }

    
    
    public function restore(User $user, Company $company): bool
    {
        return false;
    }

    
    
    public function forceDelete(User $user, Company $company): bool
    {
        return false;
    }
}
