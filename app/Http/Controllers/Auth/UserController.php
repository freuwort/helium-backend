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
    public function index(Request $request)
    {
        return PrivateUserResource::make($request->user());
    }



    public function session(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'two_factor_verified' => session('two_factor_verified', false),
        ]);
    }



    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $request->user()->updatePassword($request->new_password);

        return response(200);
    }



    public function delete(DestroyUserRequest $request)
    {
        $request->user()->delete();

        return response(200);
    }
}
