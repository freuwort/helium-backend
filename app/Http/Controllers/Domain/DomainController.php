<?php

namespace App\Http\Controllers\Domain;

use App\Classes\Permissions\Permissions;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $query = Setting::where('key', 'like', 'company_%');
        $query->orWhere('key', 'like', 'legal_%');
        $query->orWhere('key', 'like', 'registration_profiles');

        // Only authenticated users
        if ($request->user())
        {
            $query->orWhere('key', 'like', 'default_%');
            $query->orWhere('key', 'like', 'policy_%');
        }
        
        // Only authenticated admins
        if ($request->user() && $request->user()->can(Permissions::SYSTEM_ADMIN))
        {
            $query->orWhere('key', 'like', 'setup_%');
        }

        return response()->json($query->get()->mapWithKeys(fn ($setting) => [$setting->key => $setting->value]));
    }



    public function indexUnits()
    {
        return response()->json([
            'base_units' => [],
            'countries' => Country::all(),
            'currencies' => Currency::all(),
        ]);
    }
}
