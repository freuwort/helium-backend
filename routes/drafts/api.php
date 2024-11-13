<?php

use App\Http\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Content\CategoryController;
use App\Http\Controllers\Content\ContentPostController;
use App\Http\Controllers\Content\ContentSpaceController;
use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Event\EventInviteController;
use App\Http\Controllers\Form\FormController;
use App\Http\Controllers\Screen\ScreenController;
use App\Http\Controllers\Screen\ScreenDeviceController;
use App\Http\Controllers\Screen\ScreenPlaylistController;
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



    /*//////////////////////////////////////////////////////////////////////////
    CORS > CSRF > Auth > Email Verified > 2FA Verified > Enabled > Password Changed > 2FA is set up
    //////////////////////////////////////////////////////////////////////////*/
    Route::middleware(['verified', 'verified.tfa', 'enabled', 'password.changed', 'tfa.enabled'])->group(function () {
        
        // Personal User Routes
        // Route::delete('/user', [AuthUserController::class, 'delete']);



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