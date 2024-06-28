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
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }



    public function indexBasic(Request $request)
    {
        // Check if user can view models
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
        if ($request->filter_roles)
        {
            $query->whereHas('roles', function ($query) use ($request) {
                $query->whereIn('name', $request->filter_roles);
            });
        }

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
        return EditorUserResource::make($user);
    }

    
    
    public function store(CreateUserRequest $request)
    {
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
        $user->uploadProfileMedia($request->file('file'), User::MEDIA_IMAGE);
    }

    public function uploadProfileBanner(UploadProfileMediaRequest $request, User $user)
    {
        $user->uploadProfileMedia($request->file('file'), User::MEDIA_BANNER);
    }

    
    
    public function update(UpdateUserRequest $request, User $user)
    {
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
        $user->delete();
    }

    
    
    public function destroyMany(DestroyManyUserRequest $request)
    {
        $this->authorize('deleteMany', [User::class, $request->ids]);

        User::whereIn('id', $request->ids)->delete();
    }
}
