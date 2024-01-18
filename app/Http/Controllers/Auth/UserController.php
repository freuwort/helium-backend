<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DestroyUserRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Resources\User\PrivateUserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return PrivateUserResource::make($request->user());
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
