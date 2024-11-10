<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\AccountingContact;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;

class AccountingContactPolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, AccountingContact $contact): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_ACCOUNTINGCONTACTS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    

    public function view(User $user, AccountingContact $contact): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_ACCOUNTINGCONTACTS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function create(User $user): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_ACCOUNTINGCONTACTS, Permissions::APP_CREATE_ACCOUNTINGCONTACTS])) return Response::deny('You are missing the required permission.');
        
        return Response::allow();
    }



    public function update(User $user, AccountingContact $contact): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_ACCOUNTINGCONTACTS, Permissions::APP_EDIT_ACCOUNTINGCONTACTS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function delete(User $user, AccountingContact $contact): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_ACCOUNTINGCONTACTS, Permissions::APP_DELETE_ACCOUNTINGCONTACTS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function deleteMany(User $user, Collection $contacts): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_ACCOUNTINGCONTACTS, Permissions::APP_DELETE_ACCOUNTINGCONTACTS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function restore(User $user, AccountingContact $contact): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, AccountingContact $contact): Response
    {
        return Response::deny();
    }
}
