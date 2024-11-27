<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Auth\UserSettingController;
use App\Http\Controllers\Debug\DebugController;
use App\Http\Controllers\Domain\DomainController;
use App\Http\Controllers\Domain\DomainSettingController;
use App\Http\Controllers\Media\FileController;
use App\Http\Controllers\Media\DirectoryController;
use App\Http\Controllers\Media\MediaController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Permission\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserRoleController;
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



/*//////////////////////////////////////////////////////////////////////////
CORS > CSRF
//////////////////////////////////////////////////////////////////////////*/
// General domain info
Route::get('/domain/settings', [DomainController::class, 'index']);



/*//////////////////////////////////////////////////////////////////////////
CORS > CSRF > Auth
//////////////////////////////////////////////////////////////////////////*/
Route::middleware(['auth:sanctum'])->group(function () {

    // Session info
    Route::get('/session', [AuthUserController::class, 'getSession']);



    /*//////////////////////////////////////////////////////////////////////////
    CORS > CSRF > Auth > Email Verified > 2FA Verified > Enabled
    //////////////////////////////////////////////////////////////////////////*/
    Route::patch('/user/password', [AuthUserController::class, 'updatePassword'])
        ->middleware(['verified', 'verified.tfa', 'enabled']);

    Route::prefix('user/two-factor')->middleware(['verified', 'verified.tfa', 'enabled'])->group(function () {
        Route::put('/default/{method}', [TwoFactorController::class, 'setDefaultTfaMethod']);
        Route::delete('/destroy/{method}', [TwoFactorController::class, 'destroyTfaMethod']);
        
        Route::get('/backup/show', [TwoFactorController::class, 'showTfaBackupCodes']);
        Route::post('/backup/generate', [TwoFactorController::class, 'generateTfaBackupCodes']);

        Route::put('/totp/setup', [TwoFactorController::class, 'setupTfaTotp']);
        Route::put('/totp/enable', [TwoFactorController::class, 'enableTfaTotp']);
    });



    /*//////////////////////////////////////////////////////////////////////////
    CORS > CSRF > Auth > Email Verified > 2FA Verified > Enabled > Password Changed > 2FA is set up
    //////////////////////////////////////////////////////////////////////////*/
    Route::middleware(['verified', 'verified.tfa', 'enabled', 'password.changed', 'tfa.enabled'])->group(function () {
        
        // Personal User Routes
        Route::patch('/user/settings', [UserSettingController::class, 'update']);
        Route::patch('/user/settings/{key}', [UserSettingController::class, 'updateView'])->whereIn('key', ['view_[a-z0-9_]+', 'ui_[a-z0-9_]+', 'notification_[a-z0-9_]+']);
        Route::patch('/user/username', [AuthUserController::class, 'updateUsername']);
        Route::post('/user/avatar', [AuthUserController::class, 'uploadProfileAvatar']);
        Route::post('/user/banner', [AuthUserController::class, 'uploadProfileBanner']);



        // System
        Route::get('/debug', [DebugController::class, 'index']);
        Route::get('/debug/phpinfo', [DebugController::class, 'phpinfo']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/read', [NotificationController::class, 'markRead']);
        Route::patch('/notifications/unread', [NotificationController::class, 'markUnread']);

        // Domain
        Route::patch('/settings', [DomainSettingController::class, 'update']);
        Route::post('/settings/logo', [DomainSettingController::class, 'uploadLogo']);
    
        // Permissions
        Route::get('/permissions', [PermissionController::class, 'index']);
    
        // Roles
        Route::get('/roles/basic', [RoleController::class, 'indexBasic']);
        Route::post('/roles/import', [RoleController::class, 'import']);
        Route::delete('/roles', [RoleController::class, 'destroyMany']);
        Route::resource('/roles', RoleController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
    
        // Users
        Route::get('/users/basic', [UserController::class, 'indexBasic']);
        Route::post('/users/import', [UserController::class, 'import']);
        Route::post('/users/{user}/avatar', [UserController::class, 'uploadProfileAvatar']);
        Route::post('/users/{user}/banner', [UserController::class, 'uploadProfileBanner']);
        Route::patch('/users/{user}/password', [UserController::class, 'updatePassword']);
        Route::patch('/users/{user}/require-password-change', [UserController::class, 'requirePasswordChange']);
        Route::patch('/users/{user}/require-two-factor', [UserController::class, 'requireTwoFactor']);
        Route::post('/users/{user}/send-verification-email', [UserController::class, 'sendVerificationEmail']);
        Route::patch('/users/{user}/verify-email', [UserController::class, 'updateEmailVerified']);
        Route::patch('/users/{user}/verify-phone', [UserController::class, 'updatePhoneVerified']);
        Route::patch('/users/{user}/enable', [UserController::class, 'updateEnabled']);
        Route::patch('/users/{user}/block', [UserController::class, 'updateBlocked']);
        Route::put('/users/roles', [UserRoleController::class, 'assignRoles']);
        Route::delete('/users/roles', [UserRoleController::class, 'revokeRoles']);
        Route::delete('/users', [UserController::class, 'destroyMany']);
        Route::delete('/users/force', [UserController::class, 'forceDeleteMany']);
        Route::patch('/users/restore', [UserController::class, 'restoreMany']);
        Route::resource('/users', UserController::class)->only(['show', 'index', 'store', 'update']);
    
        // Media
        Route::post('/upload', [FileController::class, 'upload']);
        Route::post('/directory', [DirectoryController::class, 'store']);
        Route::get('/media/{path}', [MediaController::class, 'index'])->where('path', '(.*)');
        Route::post('/media/copy', [MediaController::class, 'copy']);
        Route::patch('/media/share', [MediaController::class, 'share']);
        Route::patch('/media/move', [MediaController::class, 'move']);
        Route::patch('/media/rename', [MediaController::class, 'rename']);
        Route::delete('/media', [MediaController::class, 'destroy']);
        Route::patch('/media/repair', [MediaController::class, 'repair']);
    });
});