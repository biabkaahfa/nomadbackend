<?php

use App\Http\Controllers\Api\Auth\JWTAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:jwt'])->group(function () {
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::post('refresh', [JWTAuthController::class, 'refresh']);
    Route::post('me', [JWTAuthController::class, 'me']);
});

Route::middleware(['guest'])->group(function () {
    Route::post('login', [JWTAuthController::class, 'login']);
});
