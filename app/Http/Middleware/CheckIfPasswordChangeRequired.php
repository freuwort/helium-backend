<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfPasswordChangeRequired
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->requires_password_change)
        {
            return response()->json(['message' => __('Password change required.')], 403);
        }

        return $next($request);
    }
}
