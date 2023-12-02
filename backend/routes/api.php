<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantsController;
use App\Http\Middleware\GoogleCaptchaMiddleware;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('restaurants')->group(function () {
Route::get('/load-map', [RestaurantsController::class, 'initMap']);
Route::get('/default-location', [RestaurantsController::class, 'getDefaultLocation']);
Route::post('/find-places', [RestaurantsController::class, 'findPlaces'])->middleware(GoogleCaptchaMiddleware::class);
Route::get('/place-photo', [RestaurantsController::class, 'placePhoto']);
});