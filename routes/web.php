<?php

use App\Http\Controllers\DefaultImage\DefaultImageController;
use App\Http\Controllers\Media\DeliveryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Media delivery
Route::get('/media/{path}', DeliveryController::class)->where('path', '(.*)')->name('media');

// Default images
Route::get('/default/{type}/{seed?}', DefaultImageController::class)->name('default.image');

require __DIR__.'/auth.php';
