<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    // START: General Methods
    public function setDefaultTwoFactorMethod(Request $request)
    {
        $request->user()->setDefaultTwoFactorMethod($request->method);

        return response()->json(['message' => __('Default two factor method set')], 204);
    }

    public function destroyTwoFactorMethod(Request $request)
    {
        $request->user()->destroyTwoFactorMethod($request->method);

        return response()->json(['message' => __('Two factor method removed')], 204);
    }
    // END: General Methods



    // START: TOTP Methods
    public function setupTotp(Request $request)
    {
        $request->user()->setupTotp();

        return response()->json([
            'secret' => $request->user()->TfaTotpMethod()->secret,
            'qr' => $request->user()->TotpProvisioningQrCode($request->user()->email ?? config('app.name')),
        ]);
    }

    public function enableTotp(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        // Check if code is valid and enable TOTP in the database
        $status = $request->user()->enableTotp($request->code);

        if (!$status) return response()->json(['message' => __($status ? 'hgghdgh' : 'Invalid code')], 422);

        // Set two factor verified session
        session([ 'two_factor_verified' => true ]);

        return response()->json(['message' => __('TOTP enabled')], 204);
    }
    // END: TOTP Methods



    // START: Verify Methods
    public function verifyTotp(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        // Check if code is valid
        $status = $request->user()->verifyTotp($request->code);

        if (!$status) return response()->json(['message' => __('Invalid code')], 422);

        // Set two factor verified session
        session([ 'two_factor_verified' => true ]);

        return response()->json(['message' => __('Two factor verified')], 204);
    }
    // END: Verify Methods
}
