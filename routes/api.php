<?php

use App\Http\Resources\User\PrivateUserResource;
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
    Route::get('/user', function (Request $request) {
        return PrivateUserResource::make($request->user());
    });
});
