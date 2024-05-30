<?php

namespace App\Http\Controllers\Domain;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index()
    {
        return response()->json([
            'company_name' => Setting::getSetting('company_name'),
            'company_legalname' => Setting::getSetting('company_legalname'),
            'company_slogan' => Setting::getSetting('company_slogan'),
            'company_logo' => Setting::getSetting('company_logo'),
            'company_favicon' => Setting::getSetting('company_favicon'),
            'default_currency' => Setting::getSetting('default_currency'),
            'default_unit_length' => Setting::getSetting('default_unit_length'),
            'default_unit_weight' => Setting::getSetting('default_unit_weight'),
            'default_unit_volume' => Setting::getSetting('default_unit_volume'),
            'default_unit_temperature' => Setting::getSetting('default_unit_temperature'),
            'default_unit_speed' => Setting::getSetting('default_unit_speed'),
            'policy_allow_registration' => Setting::getSetting('policy_allow_registration'),
            'policy_allow_password_reset' => Setting::getSetting('policy_allow_password_reset'),
            'policy_allow_password_change' => Setting::getSetting('policy_allow_password_change'),
            'policy_allow_email_change' => Setting::getSetting('policy_allow_email_change'),
            'policy_allow_username_change' => Setting::getSetting('policy_allow_username_change'),
            'policy_allow_profile_image_upload' => Setting::getSetting('policy_allow_profile_image_upload'),
            'policy_allow_profile_banner_upload' => Setting::getSetting('policy_allow_profile_banner_upload'),
            'policy_require_tfa' => Setting::getSetting('policy_require_tfa'),
        ]);
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
