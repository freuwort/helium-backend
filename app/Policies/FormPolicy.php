<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Form;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FormPolicy
{
    // TODO: Check if additional permission checks are needed
    public function basicViewAny(User $user): bool
    {
        return true;
    }



    // TODO: Check if additional permission checks are needed
    public function basicView(User $user, Form $model): bool
    {
        return true;
    }



    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::APP_VIEW_FORMS);
    }

    

    public function view(User $user, Form $model): bool
    {
        return $user->can(Permissions::APP_VIEW_FORMS);
    }



    public function create(User $user): bool
    {
        return $user->can([Permissions::APP_VIEW_FORMS, Permissions::APP_CREATE_FORMS]);
    }



    public function update(User $user, Form $model): bool
    {
        return $user->can([Permissions::APP_VIEW_FORMS, Permissions::APP_EDIT_FORMS]);
    }



    public function delete(User $user, Form $model): bool
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_FORMS, Permissions::APP_DELETE_FORMS])) return false;

        return true;
    }

    
    
    public function deleteMany(User $user, $ids): bool
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_FORMS, Permissions::APP_DELETE_FORMS])) return false;
        
        // Check every user
        foreach (Form::whereIn('id', $ids)->get() as $db_form)
        {
        }

        return true;
    }

    
    
    public function restore(User $user, Form $model): bool
    {
        return false;
    }

    
    
    public function forceDelete(User $user, Form $model): bool
    {
        return false;
    }
}
