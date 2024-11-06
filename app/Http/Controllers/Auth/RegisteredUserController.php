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
    public function store(RegisterRequest $request): Response
    {
        $validated = $request->validated();

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);

        $user = User::create($validated->user);
        
        if ($request->has_user_info) $user->user_info()->updateOrCreate([], $validated->user_info);
        if ($request->has_main_address) $user->user_info()->main_address()->create($validated->main_address);
        if ($request->has_billing_address) $user->user_info()->billing_address()->create($validated->billing_address);
        if ($request->has_shipping_address) $user->user_info()->shipping_address()->create($validated->shipping_address);

        $user->syncRoles($validated->roles);

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
