<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Resources\User\PrivateUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return PrivateUserResource::make($request->user());
    }



    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password))
        {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => ['current_password' => ['The provided password does not match your current password.']]], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response(200);
    }
}
