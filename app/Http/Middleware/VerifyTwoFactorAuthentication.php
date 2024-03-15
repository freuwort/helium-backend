<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTwoFactorAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->has_tfa_enabled && !session()->has('two_factor_verified'))
        {
            return response()->json(['message' => __('Two factor authentication is required.')], 401);
        }

        return $next($request);
    }
}
