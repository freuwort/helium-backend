<?php

namespace App\Http\Controllers\Domain;

use App\Http\Controllers\Controller;
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
            'company_logo' => Setting::getSetting('company_logo', 'https://fdbs.de/storage/media/branding/logos/logo_no_spacing.png'),
            'company_favicon' => Setting::getSetting('company_favicon'),
            'default_currency' => Setting::getSetting('default_currency'),
            'default_unit_length' => Setting::getSetting('default_unit_length'),
            'default_unit_weight' => Setting::getSetting('default_unit_weight'),
            'default_unit_volume' => Setting::getSetting('default_unit_volume'),
            'default_unit_temperature' => Setting::getSetting('default_unit_temperature'),
            'default_unit_speed' => Setting::getSetting('default_unit_speed'),
        ]);
    }
}
