<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthenticationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || ($request->user()->requires_two_factor && !$request->user()->has_tfa_enabled))
        {
            return response()->json(['message' => __('2FA is required.')], 403);
        }
        
        return $next($request);
    }
}
