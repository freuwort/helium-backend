<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Auth\UserSettingController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Content\CategoryController;
use App\Http\Controllers\Content\ContentPostController;
use App\Http\Controllers\Content\ContentSpaceController;
use App\Http\Controllers\Debug\DebugController;
use App\Http\Controllers\Domain\DomainController;
use App\Http\Controllers\Domain\DomainSettingController;
use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Event\EventInviteController;
use App\Http\Controllers\Form\FormController;
use App\Http\Controllers\Media\FileController;
use App\Http\Controllers\Media\DirectoryController;
use App\Http\Controllers\Media\MediaController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Permission\RoleController;
use App\Http\Controllers\Screen\ScreenController;
use App\Http\Controllers\Screen\ScreenDeviceController;
use App\Http\Controllers\Screen\ScreenPlaylistController;
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

// Event invite info
Route::get('/event-invite/{code}', [EventInviteController::class, 'showBasic']);



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
        Route::delete('/user', [AuthUserController::class, 'delete']);



        // System
        Route::get('/debug', [DebugController::class, 'index']);
        Route::get('/debug/phpinfo', [DebugController::class, 'phpinfo']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/read', [NotificationController::class, 'markRead']);
        Route::patch('/notifications/unread', [NotificationController::class, 'markUnread']);

        // Domain
        Route::patch('/settings', [DomainSettingController::class, 'update']);
        Route::post('/settings/logo', [DomainSettingController::class, 'uploadLogo']);
        
        // Base units
        Route::get('/domain/units', [DomainController::class, 'indexUnits']);
    
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
        Route::patch('/users/{user}/enable', [UserController::class, 'updateEnabled']);
        Route::put('/users/roles', [UserRoleController::class, 'assignRoles']);
        Route::delete('/users/roles', [UserRoleController::class, 'revokeRoles']);
        Route::delete('/users', [UserController::class, 'destroyMany']);
        Route::resource('/users', UserController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
    
        // Companies
        Route::post('/companies/{company}/logo', [CompanyController::class, 'uploadLogo']);
        Route::post('/companies/{company}/banner', [CompanyController::class, 'uploadBanner']);
        Route::delete('/companies', [CompanyController::class, 'destroyMany']);
        Route::resource('/companies', CompanyController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
    
        // Media
        Route::post('/upload', [FileController::class, 'upload']);
        Route::post('/directory', [DirectoryController::class, 'store']);
        Route::get('/media/{path}', [MediaController::class, 'index'])->where('path', '(.*)');
        Route::post('/media/copy', [MediaController::class, 'copy']);
        Route::patch('/media/share', [MediaController::class, 'share']);
        Route::patch('/media/move', [MediaController::class, 'move']);
        Route::patch('/media/rename', [MediaController::class, 'rename']);
        Route::delete('/media', [MediaController::class, 'destroy']);
        Route::patch('/media/discover', [MediaController::class, 'discover']);



        // Form
        Route::delete('/forms', [FormController::class, 'destroyMany']);
        Route::resource('/forms', FormController::class)->only(['show', 'index', 'store', 'update', 'destroy']);



        // Event
        Route::post('/events/{event}/invites/import', [EventInviteController::class, 'import']);
        Route::post('/events/{event}/invites/export', [EventInviteController::class, 'export']);
        Route::patch('/events/{event}/invites/email', [EventInviteController::class, 'sendTemplatedEmail']);
        Route::delete('/events/{event}/invites', [EventInviteController::class, 'destroyMany']);
        Route::resource('/events/{event}/invites', EventInviteController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
        
        Route::delete('/events', [EventController::class, 'destroyMany']);
        Route::resource('/events', EventController::class)->only(['show', 'index', 'store', 'update', 'destroy']);

        // Event invite
        Route::patch('/event-invite/{code}/claim', [EventInviteController::class, 'claim']);
        Route::patch('/event-invite/{code}/status', [EventInviteController::class, 'updateStatus']);
        Route::post('/event-invite/{code}/details', [EventInviteController::class, 'updateDetails']);



        // Screens
        Route::delete('/screens/devices', [ScreenDeviceController::class, 'destroyMany']);
        Route::resource('/screens/devices', ScreenDeviceController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
        
        Route::delete('/screens/playlists', [ScreenPlaylistController::class, 'destroyMany']);
        Route::resource('/screens/playlists', ScreenPlaylistController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
        
        Route::delete('/screens', [ScreenController::class, 'destroyMany']);
        Route::resource('/screens', ScreenController::class)->only(['show', 'index', 'store', 'update', 'destroy']);



        // Content
        Route::patch('/content/posts/{postGroup}/review', [ContentPostController::class, 'updateReviewStatus']);
        Route::patch('/content/posts/{postGroup}/approve', [ContentPostController::class, 'approveDraft']);
        Route::delete('/content/posts', [ContentPostController::class, 'destroyMany']);
        Route::resource('/content/posts', ContentPostController::class)->only(['show', 'index', 'store', 'update', 'destroy'])->parameters(['posts' => 'postGroup']);
        
        Route::delete('/content/spaces', [ContentSpaceController::class, 'destroyMany']);
        Route::resource('/content/spaces', ContentSpaceController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
        
        Route::delete('/categories', [CategoryController::class, 'destroyMany']);
        Route::resource('/categories', CategoryController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
    });
});