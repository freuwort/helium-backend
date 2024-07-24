<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadProfileMediaRequest;
use App\Http\Requests\User\DestroyManyUserRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\Role\BasicRoleResource;
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
    // public function __construct()
    // {
    //     $this->authorizeResource(User::class, 'user');
    // }



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
        $query = User::with('user_name', 'user_company', 'roles', 'permissions', 'media');

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
        $this->authorize('create', [User::class, Role::whereIn('id', $request->roles)->get()]);

        $user = User::create($request->model);

        // Update password if set
        if ($request->password) $user->updatePassword($request->password);

        $user->user_name()->updateOrCreate([], $request->user_name);
        $user->user_company()->updateOrCreate([], $request->user_company);
        $user->syncMany(Identifier::class, $request->identifiers);
        $user->syncMany(Address::class, $request->addresses);
        $user->syncMany(BankConnection::class, $request->bank_connections, 'bank_connections');
        $user->syncMany(Email::class, $request->emails);
        $user->syncMany(Phonenumber::class, $request->phonenumbers);
        $user->syncMany(Date::class, $request->dates);
        $user->syncMany(Link::class, $request->links);

        // Update roles
        $user->syncRoles($request->roles);

        return EditorUserResource::make($user);
    }

    public function uploadProfileImage(UploadProfileMediaRequest $request, User $user)
    {
        // TODO: Add policy
        $user->uploadProfileMedia($request->file('file'), User::MEDIA_IMAGE);
    }

    public function uploadProfileBanner(UploadProfileMediaRequest $request, User $user)
    {
        // TODO: Add policy
        $user->uploadProfileMedia($request->file('file'), User::MEDIA_BANNER);
    }

    
    
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', [$user, Role::whereIn('id', $request->roles)->get()]);

        $user->update($request->model);

        // Update password if set
        if ($request->password) $user->updatePassword($request->password);

        $user->user_name()->updateOrCreate([], $request->user_name);
        $user->user_company()->updateOrCreate([], $request->user_company);
        $user->syncMany(Identifier::class, $request->identifiers);
        $user->syncMany(Address::class, $request->addresses);
        $user->syncMany(BankConnection::class, $request->bank_connections, 'bank_connections');
        $user->syncMany(Email::class, $request->emails);
        $user->syncMany(Phonenumber::class, $request->phonenumbers);
        $user->syncMany(Date::class, $request->dates);
        $user->syncMany(Link::class, $request->links);

        // Update roles
        $user->syncRoles($request->roles);

        return EditorUserResource::make($user);
    }

    
    
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
    }

    
    
    public function destroyMany(DestroyManyUserRequest $request)
    {
        $users = User::whereIn('id', $request->ids);

        $this->authorize('deleteMany', [User::class, $users->get()]);

        $users->delete();
    }
}
