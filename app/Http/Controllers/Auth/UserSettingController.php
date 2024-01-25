<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserSettingRequest;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{
    // Set a setting for the authenticated User model.
    public function update(UpdateUserSettingRequest $request)
    {
        $request->user()->setSetting($request->validated());

        return response(200);
    }
}
