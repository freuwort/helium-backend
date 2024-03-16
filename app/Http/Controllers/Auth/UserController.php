<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DestroyUserRequest;
use App\Http\Requests\Auth\UpdateUserPasswordRequest;
use App\Http\Resources\User\PrivateUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUser()
    {
        return PrivateUserResource::make(Auth::user());
    }



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



    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $request->user()->updatePassword($request->new_password);

        return response()->json(['message' => __('Password updated successfully')]);
    }



    public function delete(DestroyUserRequest $request)
    {
        $request->user()->delete();

        return response()->json(['message' => __('User deleted successfully')]);
    }
}
