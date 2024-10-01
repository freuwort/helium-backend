<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Setting::getSetting('policy_debug_mode', false)) {
            return response()->json(['message' => 'Debug mode is not enabled.'], 403);
        }

        return $next($request);
    }
}
