<?php

namespace App\Http\Controllers\Debug;

use App\Classes\Permissions\Permissions;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:'.Permissions::SYSTEM_ADMIN]);
    }

    public function index(Request $request)
    {
        return response()->json([
            'settings' => Setting::all()->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->value];
            }),
            'phpinfo_url' => url('/api/debug/phpinfo'),
        ]);
    }

    public function phpinfo()
    {
        phpinfo();
    }
}
