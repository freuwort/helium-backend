<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\PrivateUserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Get the authenticated User model.
    public function index(Request $request)
    {
        return PrivateUserResource::make($request->user());
    }
}
