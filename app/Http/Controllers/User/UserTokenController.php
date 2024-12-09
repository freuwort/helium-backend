<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserTokenController extends Controller
{
    public function store(Request $request, User $user)
    {
        $this->authorize('adminAction', $user);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token = $user->createToken($request->name);

        return response()->json(['token' => $token->plainTextToken]);
    }

    public function destroy(Request $request, User $user, $token)
    {
        $this->authorize('adminAction', $user);

        $user->tokens()->where('id', $token)->delete();
    }
}
