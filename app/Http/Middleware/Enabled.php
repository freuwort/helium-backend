<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Enabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->enabled_at || $request->user()->blocked_at)
        {
            return response()->json(['message' => __('Your account is disabled or blocked.')], 403);
        }

        return $next($request);
    }
}
