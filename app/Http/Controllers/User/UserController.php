<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadProfileMediaRequest;
use App\Http\Requests\User\DestroyManyUserRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\EnableUserRequest;
use App\Http\Requests\User\ImportUsersRequest;
use App\Http\Requests\User\RequirePasswordChangeRequest;
use App\Http\Requests\User\RequireTwoFactorRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\VerifyUserEmailRequest;
use App\Http\Resources\User\EditorUserResource;
use App\Http\Resources\User\BasicUserResource;
use App\Http\Resources\User\UserResource;
use App\Models\Address;
use App\Models\BankConnection;
use App\Models\Date;
use App\Models\Email;
use App\Models\Identifier;
use App\Models\Link;
use App\Models\Phonenumber;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function indexBasic(Request $request)
    {
        $this->authorize('basicViewAny', User::class);

        // Base query
        $query = User::query();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('username', $request->filter_search)
                    ->orWhereFuzzy('email', $request->filter_search);
            });
        }

        // Filter
        if ($request->filter_exclude)
        {
            $query->whereNotIn('id', $request->filter_exclude);
        }

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return BasicUserResource::collection($query->paginate($request->size ?? 20));
    }



    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        // Base query
        $query = User::with(['media', 'roles', 'permissions', 'roles.permissions']);

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('username', $request->filter_search)
                    ->orWhereFuzzy('email', $request->filter_search);
            });
        }

        // Filter
        if ($request->filter_exclude)
        {
            $query->whereNotIn('id', $request->filter_exclude);
        }

        if ($request->filter_roles)
        {
            $query->whereHas('roles', function ($query) use ($request) {
                $query->whereIn('name', $request->filter_roles);
            });
        }

        if ($request->filter_permission_levels)
        {
            $query->where(function ($query) use ($request) {
                if (in_array('admin', $request->filter_permission_levels)) $query->orWhere(fn ($query) => $query->whereIsAdmin());
                if (in_array('elevated', $request->filter_permission_levels)) $query->orWhere(fn ($query) => $query->whereHasElevatedPermissions());
            });
        }

        if ($request->filter_email_verified === 'pending') $query->whereEmailVerified(false);
        if ($request->filter_email_verified === 'active') $query->whereEmailVerified(true);
        
        if ($request->filter_enabled === 'pending') $query->whereEnabled(false);
        if ($request->filter_enabled === 'active') $query->whereEnabled(true);

        if ($request->filter_tfa_enabled === 'inactive') $query->whereTfaEnabled(false);
        if ($request->filter_tfa_enabled === 'active') $query->whereTfaEnabled(true);

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return UserResource::collection($query->paginate($request->size ?? 20))
        ->additional(['keys' => $query->pluck('id')->toArray()])
        ->additional(['filter_values' => [
            'roles' => Role::all()->pluck('name')->toArray(),
        ]]);
    }

    
    
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return EditorUserResource::make($user);
    }

    
    
    public function store(CreateUserRequest $request)
    {
        $this->authorize('create', [User::class, Role::whereIn('id', $request->validated('roles'))->get()]);

        // Create model
        $user = User::create($request->validated('model'));

        // Sync model related data
        $user->user_name()->updateOrCreate([],      $request->validated('user_name'));
        $user->user_company()->updateOrCreate([],   $request->validated('user_company'));
        $user->syncMany(Identifier::class,          $request->validated('identifiers'));
        $user->syncMany(Address::class,             $request->validated('addresses'));
        $user->syncMany(BankConnection::class,      $request->validated('bank_connections'), 'bank_connections');
        $user->syncMany(Email::class,               $request->validated('emails'));
        $user->syncMany(Phonenumber::class,         $request->validated('phonenumbers'));
        $user->syncMany(Date::class,                $request->validated('dates'));
        $user->syncMany(Link::class,                $request->validated('links'));

        // Sync roles
        $user->syncRoles($request->validated('roles'));

        return EditorUserResource::make($user);
    }



    public function import(ImportUsersRequest $request)
    {
        $this->authorize('import', [User::class, $request->items]);

        foreach ($request->items as $item)
        {
            $user = User::create($item);

            $user->user_name()->updateOrCreate([], $item['user_name']);
            $user->user_company()->updateOrCreate([], $item['user_company']);

            $user->syncRoles($item['roles']);
        }
    }

    
    
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', [$user, Role::whereIn('id', $request->validated('roles'))->get()]);

        // Update model
        $user->update($request->validated('model'));

        // Sync model related data
        $user->user_name()->updateOrCreate([],      $request->validated('user_name'));
        $user->user_company()->updateOrCreate([],   $request->validated('user_company'));
        $user->syncMany(Identifier::class,          $request->validated('identifiers'));
        $user->syncMany(Address::class,             $request->validated('addresses'));
        $user->syncMany(BankConnection::class,      $request->validated('bank_connections'), 'bank_connections');
        $user->syncMany(Email::class,               $request->validated('emails'));
        $user->syncMany(Phonenumber::class,         $request->validated('phonenumbers'));
        $user->syncMany(Date::class,                $request->validated('dates'));
        $user->syncMany(Link::class,                $request->validated('links'));

        // Sync roles
        $user->syncRoles($request->validated('roles'));

        return EditorUserResource::make($user->fresh());
    }



    public function updatePassword(UpdateUserPasswordRequest $request, User $user)
    {
        $this->authorize('updatePassword', $user);

        $user->updatePassword($request->validated('password'));
    }



    public function requirePasswordChange(RequirePasswordChangeRequest $request, User $user)
    {
        $this->authorize('requirePasswordChange', $user);

        $user->update(['requires_password_change' => $request->validated('requires_password_change')]);
    }



    public function requireTwoFactor(RequireTwoFactorRequest $request, User $user)
    {
        $this->authorize('requireTwoFactor', $user);

        $user->update(['requires_two_factor' => $request->validated('requires_two_factor')]);
    }



    public function updateEmailVerified(VerifyUserEmailRequest $request, User $user)
    {
        $this->authorize('verifyEmail', $user);

        $user->verifyEmail($request->validated('email_verified'));
    }



    public function updateEnabled(EnableUserRequest $request, User $user)
    {
        $this->authorize('enable', $user);

        $user->enable($request->validated('enabled'));
    }



    public function uploadProfileAvatar(UploadProfileMediaRequest $request, User $user)
    {
        $this->authorize('uploadAvatar', $user);

        $user->uploadProfileMedia($request->file('file'), 'avatar');
    }



    public function uploadProfileBanner(UploadProfileMediaRequest $request, User $user)
    {
        $this->authorize('uploadBanner', $user);

        $user->uploadProfileMedia($request->file('file'), 'banner');
    }

    
    
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
    }

    
    
    public function destroyMany(DestroyManyUserRequest $request)
    {
        $users = User::whereIn('id', $request->validated('ids'));

        $this->authorize('deleteMany', [User::class, $users->get()]);

        $users->delete();
    }
}
