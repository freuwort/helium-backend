<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Enabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->is_enabled)
        {
            return response()->json(['message' => __('Your account is disabled.')], 403);
        }

        return $next($request);
    }
}
