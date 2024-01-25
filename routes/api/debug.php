<?php

use App\Http\Controllers\Debug\DebugController;
use Illuminate\Support\Facades\Route;

Route::prefix('debug')->group(function () {
    Route::get('/status/{status}', [DebugController::class, 'status']);
});