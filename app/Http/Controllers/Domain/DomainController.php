<?php

namespace App\Http\Controllers\Domain;

use App\Classes\Permissions\Permissions;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\Unit;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        // Settings
        $settings = Setting::where('key', 'like', 'company_%');
        $settings->orWhere('key', 'like', 'legal_%');
        $settings->orWhere('key', 'like', 'registration_profiles');
        $settings->orWhere('key', 'like', 'policy_allow_registration');

        // Settings only authenticated users
        if ($request->user())
        {
            $settings->orWhere('key', 'like', 'default_%');
            $settings->orWhere('key', 'like', 'policy_%');
        }
        
        // Settings only authenticated admins
        if ($request->user() && $request->user()->can(Permissions::SYSTEM_ADMIN))
        {
            $settings->orWhere('key', 'like', 'setup_%');
        }

        $settings = $settings->get()->mapWithKeys(fn ($setting) => [$setting->key => $setting->value]);


        // Units
        $units = Unit::orderBy('type')->get();

        // Countries
        $countries = Country::orderBy('name')->get();

        // Currencies
        $currencies = Currency::orderBy('name')->get();


        return response()->json(
            collect([])
            ->merge($settings)
            ->merge([
                'units' => $units,
                'countries' => $countries,
                'currencies' => $currencies,
            ])
        );
    }
}
