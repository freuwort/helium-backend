<?php

use App\Http\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Auth\UserSettingController;
use App\Http\Controllers\Domain\DomainController;
use App\Http\Controllers\Domain\DomainSettingController;
use App\Http\Controllers\User\UserController;
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
    Route::get('/settings', [DomainController::class, 'index']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [AuthUserController::class, 'index']);

        Route::patch('/settings', [UserSettingController::class, 'update']);

        Route::patch('/password', [AuthUserController::class, 'updatePassword']);

        Route::delete('/', [AuthUserController::class, 'delete']);
    });

    Route::prefix('/settings')->group(function () {
        Route::patch('/', [DomainSettingController::class, 'update']);
    });



    Route::resource('/users', UserController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
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