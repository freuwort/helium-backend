<?php

use App\Http\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Auth\UserSettingController;
use App\Http\Controllers\Domain\DomainController;
use App\Http\Controllers\Domain\DomainSettingController;
use App\Http\Controllers\User\UserController;
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

require __DIR__ . '/api/debug.php';

// Public Domain Routes
Route::prefix('domain')->group(function () {
    Route::get('/settings', [DomainController::class, 'index']);
});

// Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User (self)
    Route::prefix('user')->group(function () {
        Route::get('/', [AuthUserController::class, 'index']);
        Route::patch('/settings', [UserSettingController::class, 'update']);
        Route::patch('/password', [AuthUserController::class, 'updatePassword']);
        Route::delete('/', [AuthUserController::class, 'delete']);
    });

    // Domain
    Route::patch('/settings', [DomainSettingController::class, 'update']);

    // Users
    Route::resource('/users', UserController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
    Route::delete('/users', [UserController::class, 'destroyMany']);
});