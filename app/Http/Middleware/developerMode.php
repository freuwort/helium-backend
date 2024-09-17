<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class developerMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Setting::getSetting('policy_developer_mode', false)) {
            return response()->json(['message' => 'Developer mode is not enabled.'], 403);
        }

        return $next($request);
    }
}
