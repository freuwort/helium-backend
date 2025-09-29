<?php

namespace App\Http\Controllers\User;

use App\Events\UserBlocked;
use App\Events\UserEnabled;
use App\Events\UserUnblocked;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadProfileMediaRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\ImportUsersRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\BasicUserResource;
use App\Http\Resources\User\UserResource;
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
            $query->where(function ($query) use ($request) {
                $query
                    ->orWhereRaw("username % ?", [$request->filter_search])->orWhere('username', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("email % ?", [$request->filter_search])->orWhere('email', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("name % ?", [$request->filter_search])->orWhere('name', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("organisation % ?", [$request->filter_search])->orWhere('organisation', 'ILIKE', "%$request->filter_search%");
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
        $query = User::with(['roles', 'permissions', 'roles.permissions']);

        // Search
        if ($request->filter_search)
        {
            $query->where(function ($query) use ($request) {
                $query
                    ->orWhereRaw("name % ?", [$request->filter_search])->orWhere('name', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("username % ?", [$request->filter_search])->orWhere('username', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("email % ?", [$request->filter_search])->orWhere('email', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("phone % ?", [$request->filter_search])->orWhere('phone', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("customer_id % ?", [$request->filter_search])->orWhere('customer_id', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("employee_id % ?", [$request->filter_search])->orWhere('employee_id', 'ILIKE', "%$request->filter_search%")
                    ->orWhereRaw("member_id % ?", [$request->filter_search])->orWhere('member_id', 'ILIKE', "%$request->filter_search%");
            });
        }

        // Filter
        if ($request->filter_exclude) {
            $query->whereNotIn('id', $request->filter_exclude);
        }

        if ($request->filter_deleted) {
            $query->onlyTrashed();
        }

        if ($request->filter_roles) {
            $query->whereHas('roles', function ($query) use ($request) {
                $query->whereIn('name', $request->filter_roles);
            });
        }

        if ($request->filter_permission_levels) {
            $query->where(function ($query) use ($request) {
                if (in_array('admin', $request->filter_permission_levels)) $query->orWhere(fn ($query) => $query->whereIsAdmin());
                if (in_array('elevated', $request->filter_permission_levels)) $query->orWhere(fn ($query) => $query->whereHasElevatedPermissions());
            });
        }

        if ($request->filter_email_verified === 'pending') $query->whereEmailVerified(false);
        if ($request->filter_email_verified === 'active') $query->whereEmailVerified(true);

        if ($request->filter_phone_verified === 'pending') $query->wherePhoneVerified(false);
        if ($request->filter_phone_verified === 'active') $query->wherePhoneVerified(true);
        
        if ($request->filter_enabled === 'pending') $query->whereEnabled(false);
        if ($request->filter_enabled === 'active') $query->whereEnabled(true);

        if ($request->filter_blocked === 'inactive') $query->whereBlocked(false);
        if ($request->filter_blocked === 'active') $query->whereBlocked(true);

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

        return UserResource::make($user);
    }

    
    
    public function store(CreateUserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = User::create($request->validated());

        return UserResource::make($user);
    }



    public function import(ImportUsersRequest $request)
    {
        $this->authorize('import', [User::class, $request->items]);

        foreach ($request->items as $item)
        {
            $user = User::create($item);
            $user->syncRoles($item['roles']);
        }
    }

    
    
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update($request->validated());
        $user->updateAddress('main', $request->main_address);
        $user->updateAddress('billing', $request->billing_address);
        $user->updateAddress('shipping', $request->shipping_address);

        return UserResource::make($user->fresh());
    }



    public function updatePassword(Request $request, User $user)
    {
        $this->authorize('adminAction', $user);
        
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->updatePassword($request->password);
    }



    public function requirePasswordChange(Request $request, User $user)
    {
        $this->authorize('adminAction', $user);
        
        $request->validate([
            'requires_password_change' => ['required', 'bool'],
        ]);

        $user->update(['requires_password_change' => $request->requires_password_change]);
    }



    public function requireTwoFactor(Request $request, User $user)
    {
        $this->authorize('adminAction', $user);
        
        $request->validate([
            'requires_two_factor' => ['required', 'bool'],
        ]);

        $user->update(['requires_two_factor' => $request->requires_two_factor]);
    }



    public function sendVerificationEmail(Request $request, User $user)
    {
        $this->authorize('sendVerificationEmail', $user);

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
    }



    public function updateEmailVerified(Request $request, User $user)
    {
        $this->authorize('adminAction', $user);
        
        $request->validate([
            'email_verified' => ['required', 'bool'],
        ]);

        $user->verifyEmail($request->email_verified);
    }



    public function updatePhoneVerified(Request $request, User $user)
    {
        $this->authorize('adminAction', $user);
        
        $request->validate([
            'phone_verified' => ['required', 'bool'],
        ]);

        $user->verifyPhone($request->phone_verified);
    }



    public function updateEnabled(Request $request, User $user)
    {
        $this->authorize('enable', $user);

        $request->validate([
            'enabled' => ['required', 'bool'],
        ]);

        if ($request->enabled) event(new UserEnabled($user));

        $user->enable($request->enabled);
    }



    public function updateBlocked(Request $request, User $user)
    {
        $this->authorize('enable', $user);
        
        $request->validate([
            'blocked' => ['required', 'bool'],
            'block_reason' => ['nullable', 'string'],
        ]);

        $request->blocked ?
            event(new UserBlocked($user, $request->block_reason)) :
            event(new UserUnblocked($user));

        $user->block($request->blocked, $request->block_reason);
    }



    public function uploadProfileAvatar(UploadProfileMediaRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->uploadProfileMedia('avatar', $request->file('file'));
    }



    public function uploadProfileBanner(UploadProfileMediaRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->uploadProfileMedia('banner', $request->file('file'));
    }

    
    
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
    }

    
    
    public function destroyMany(Request $request)
    {
        $request->validate(['ids.*' => ['required', 'integer', 'exists:users,id']]);
        
        $users = User::whereIn('id', $request->ids);

        $this->authorize('deleteMany', [User::class, $users->get()]);

        $users->delete();
    }



    public function restoreMany(Request $request)
    {
        $request->validate(['ids.*' => ['required', 'integer', 'exists:users,id']]);
        
        $users = User::withTrashed()->whereIn('id', $request->ids);

        $this->authorize('restoreMany', [User::class, $users->get()]);

        $users->restore();
    }



    public function forceDeleteMany(Request $request)
    {
        $request->validate(['ids.*' => ['required', 'integer', 'exists:users,id']]);
        
        $users = User::withTrashed()->whereIn('id', $request->ids);

        $this->authorize('deleteMany', [User::class, $users->get()]);

        $users->forceDelete();
    }
}
