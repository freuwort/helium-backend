<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DestroyUserRequest;
use App\Http\Requests\Auth\UpdateNameRequest;
use App\Http\Requests\Auth\UpdateUsernameRequest;
use App\Http\Requests\Auth\UpdateUserPasswordRequest;
use App\Http\Requests\UploadProfileMediaRequest;
use App\Http\Resources\User\PrivateUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getSession()
    {
        $user = Auth::user();
        $authenticated = Auth::check();
        $tfa_enabled = $user->has_tfa_enabled;
        $tfa_verified = session('two_factor_verified', false);

        return response()->json([
            'user' => PrivateUserResource::make($user),
            'session' => [
                'authenticated' => $authenticated,
                'tfa_enabled' => $tfa_enabled,
                'tfa_verified' => $tfa_verified,
            ],
        ]);
    }



    public function updateName(UpdateNameRequest $request)
    {
        $request->user()->update($request->validated());
        // TODO: Add event
    }



    public function updateUsername(UpdateUsernameRequest $request)
    {
        $request->user()->update(['username' => $request->validated('username')]);
        // TODO: Add event
    }



    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $request->user()->updatePassword($request->validated('new_password'));
        $request->user()->update(['requires_password_change' => false]);

        return response()->json(['message' => __('Password updated successfully')]);
    }



    public function uploadProfileAvatar(UploadProfileMediaRequest $request)
    {
        $this->authorize('uploadAvatar', $request->user());

        $request->user()->uploadProfileMedia('avatar', $request->file('file'));
    }



    public function uploadProfileBanner(UploadProfileMediaRequest $request)
    {
        $this->authorize('uploadBanner', $request->user());

        $request->user()->uploadProfileMedia('banner', $request->file('file'));
    }



    public function delete(DestroyUserRequest $request)
    {
        $request->user()->delete();

        return response()->json(['message' => __('User deleted successfully')]);
    }
}
