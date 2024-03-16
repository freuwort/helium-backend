<?php

namespace App\Http\Controllers\Auth;

use App\Events\TwoFactorMethodEnabled;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    // START: General Methods
    public function setDefaultTfaMethod(Request $request)
    {
        $request->user()->setDefaultTfaMethod($request->method);

        return response()->json(['message' => __('Default two factor method set')]);
    }

    public function destroyTfaMethod(Request $request)
    {
        $request->user()->destroyTfaMethod($request->method);

        return response()->json(['message' => __('Two factor method removed')]);
    }
    // END: General Methods



    // START: TOTP Methods
    public function setupTfaTotp(Request $request)
    {
        $request->user()->setupTfaTotp();

        return response()->json([
            'secret' => optional($request->user()->tfa_totp_method)->secret,
            'qr' => $request->user()->TfaTotpQrCode($request->user()->email ?? config('app.name')),
        ]);
    }

    public function enableTfaTotp(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        // Check if code is valid and enable TOTP in the database
        $method = $request->user()->enableTfaTotp($request->code);

        if (!$method) return response()->json(['message' => __('Invalid code')], 422);

        // Set two factor verified session
        session([ 'two_factor_verified' => true ]);

        event(new TwoFactorMethodEnabled($method, $request->user()));

        return response()->json(['message' => __('TOTP enabled')]);
    }

    public function verifyTfaTotp(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        // Check if code is valid
        $status = $request->user()->verifyTfaTotp($request->code);

        if (!$status) return response()->json(['message' => __('Invalid code')], 422);

        // Set two factor verified session
        session([ 'two_factor_verified' => true ]);

        return response()->json(['message' => __('Two factor verified')]);
    }
    // END: TOTP Methods



    // START: Backup Codes Methods
    public function generateTfaBackupCodes(Request $request)
    {
        $request->user()->generateTfaBackupCodes();

        return response()->json([
            'codes' => $request->user()->twoFactorBackupCodes()->pluck('code'),
        ]);
    }

    public function showTfaBackupCodes(Request $request)
    {
        return response()->json([
            'codes' => $request->user()->twoFactorBackupCodes()->pluck('code'),
        ]);
    }

    public function verifyTfaBackupCode(Request $request)
    {
        $request->validate([
            'code' => 'required|size:8',
        ]);

        // Check if code is valid
        $status = $request->user()->verifyTfaBackupCode($request->code);

        if (!$status) return response()->json(['message' => __('Invalid code')], 422);

        // Set two factor verified session
        session([ 'two_factor_verified' => true ]);

        return response()->json(['message' => __('Two factor verified')]);
    }
    // END: Backup Codes Methods
}
