<?php

namespace App\Http\Controllers\Domain;

use App\Classes\Permissions\Permissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Domain\UpdateDomainSettingRequest;
use App\Http\Requests\UploadProfileMediaRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class DomainSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:'.Permissions::SYSTEM_ADMIN);
    }

    public function update(UpdateDomainSettingRequest $request)
    {
        Setting::setSetting($request->validated());
    }

    public function uploadLogo(UploadProfileMediaRequest $request)
    {
        $file = $request->file('file');
        $name = 'logo.'.$file->getClientOriginalExtension();
        
        // Upload to storage
        Storage::putFileAs('public/branding', $file, $name);

        // Set as logo
        Setting::setSetting('company_logo', url('/storage/branding/'.$name));
    }
}
