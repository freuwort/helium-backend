<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Form;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;

class FormPolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, Form $form): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_FORMS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    

    public function view(User $user, Form $form): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_FORMS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function create(User $user): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_FORMS, Permissions::APP_CREATE_FORMS])) return Response::deny('You are missing the required permission.');
        
        return Response::allow();
    }



    public function update(User $user, Form $form): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_FORMS, Permissions::APP_EDIT_FORMS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function delete(User $user, Form $form): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_FORMS, Permissions::APP_DELETE_FORMS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function deleteMany(User $user, Collection $forms): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_FORMS, Permissions::APP_DELETE_FORMS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function restore(User $user, Form $form): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, Form $form): Response
    {
        return Response::deny();
    }
}
