<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\UserSettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('domain')->group(function () {
    Route::get('/company-meta', function (Request $request) {
        return response()->json([
            'name' => 'FDBS',
            'legalName' => 'Fleischer Dienst Braunschweig eG',
            'slogan' => 'the competence in foodservice',
            'logo' => 'https://fdbs.de/storage/media/branding/logos/logo_no_spacing.png',
            'favicon' => '',
        ]);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);

        Route::patch('/settings', [UserSettingController::class, 'update']);
    });
});



Route::prefix('debug')->group(function () {
    Route::get('/status/{status}', function (Request $request) {
        
        if ($request->status == 422)
        {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);
        }
        
        return response()->json(['message' => 'This is a message from the server. code_'. $request->status], $request->status);
    });
});