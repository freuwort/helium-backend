<?php

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

require __DIR__.'/auth.php';
