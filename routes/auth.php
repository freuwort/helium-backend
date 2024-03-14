<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware(['throttle:6,1'])->name('verification.send');
    
    Route::prefix('two-factor')->group(function () {
        Route::put('/default/{method}', [TwoFactorController::class, 'setDefaultTfaMethod'])->name('two-factor.default');
        Route::delete('/destroy/{method}', [TwoFactorController::class, 'destroyTfaMethod'])->name('two-factor.destroy');
        
        Route::prefix('backup')->group(function () {
            Route::get('/show', [TwoFactorController::class, 'showTfaBackupCodes'])->name('two-factor.backup.show');
            Route::post('/generate', [TwoFactorController::class, 'generateTfaBackupCodes'])->name('two-factor.backup.regenerate');
            Route::post('/verify', [TwoFactorController::class, 'verifyTfaBackupCode'])->name('two-factor.backup.verify');
        });

        Route::prefix('totp')->group(function () {
            Route::put('/setup', [TwoFactorController::class, 'setupTfaTotp'])->name('two-factor.totp.setup');
            Route::put('/enable', [TwoFactorController::class, 'enableTfaTotp'])->name('two-factor.totp.enable');
            Route::post('/verify', [TwoFactorController::class, 'verifyTfaTotp'])->name('two-factor.totp.verify');
        });
    });
    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
