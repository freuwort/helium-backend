<?php

namespace App\Http\Controllers\Debug;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function status(Request $request)
    {
        if ($request->status == 422)
        {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
            ]);
        }

        return response()->json([
            'message' => 'Debug status set to ' . $request->status,
        ], $request->status);
    }
}
