<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserSettingRequest;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{
    public function update(UpdateUserSettingRequest $request)
    {
        $request->user()->setSetting($request->validated());

        return response(200);
    }

    public function updateView(Request $request)
    {
        $request->user()->setSetting($request->key, $request->value);
    }
}
