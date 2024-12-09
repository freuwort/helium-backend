<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTwoFactorAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware if request doesn't have session
        if (!$request->hasSession()) {
            return $next($request);
        }

        // Skip middleware if user doesn't have TFA enabled
        if (!$request->user()->has_tfa_enabled) {
            return $next($request);
        }

        // Check if user has verified TFA
        if (!session()->has('two_factor_verified')) {
            return response()->json(['message' => __('Two factor authentication is required.')], 401);
        }

        return $next($request);
    }
}
