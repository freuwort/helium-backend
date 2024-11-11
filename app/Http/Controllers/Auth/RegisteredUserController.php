<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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
        $user = User::create($validated['user']);
        
        // Administrative actions
        if ($validated['auto_enable']) $user->enable();
        if (count($validated['roles'])) $user->syncRoles($validated['roles']);

        // Assign addresses
        if ($validated['main_address']) $user->main_address()->create($validated['main_address']);
        if ($validated['billing_address']) $user->billing_address()->create($validated['billing_address']);
        if ($validated['shipping_address']) $user->shipping_address()->create($validated['shipping_address']);

        event(new Registered($user));

        Auth::login($user);

        // return response()->noContent();
        return response()->json($validated);
    }
}
