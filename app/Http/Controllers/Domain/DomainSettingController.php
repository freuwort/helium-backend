<?php

namespace App\Http\Controllers\Domain;

use App\Http\Controllers\Controller;
use App\Http\Requests\Domain\UpdateDomainSettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

class DomainSettingController extends Controller
{
    // Set a setting for the domain.
    public function update(UpdateDomainSettingRequest $request)
    {
        Setting::setSetting($request->validated());

        return response(200);
    }
}
