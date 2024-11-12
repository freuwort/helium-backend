<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        // Create user
        $user = User::create($validated);
        
        if (isset($validated['auto_enable'])) $user->enable();
        if (isset($validated['roles'])) $user->syncRoles($validated['roles']);
        if (isset($validated['main_address'])) $user->updateAddress('main', $validated['main_address']);
        if (isset($validated['billing_address'])) $user->updateAddress('billing', $validated['billing_address']);
        if (isset($validated['shipping_address'])) $user->updateAddress('shipping', $validated['shipping_address']);

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
