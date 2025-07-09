<?php

use App\Http\Controllers\Api\Auth\JWTAuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CompagnieController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\PaiementCallbackController;

//

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


Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth:jwt')->get('/reservations/{id}/recapitulatif', [ReservationsController::class, 'recapitulatif']);



Route::middleware('auth:jwt')->group(function () {




});
Route::middleware('auth:jwt')->get('/stats/utilisateur', [StatsController::class, 'statsUtilisateur']);

Route::post('/reservations/recapitulatif', [ReservationsController::class, 'recapitulatifTemporaire']);


// routes/api.php

    Route::get('/destinations/public', [DestinationController::class, 'destinationsPubliques']);
    Route::get('/destinations/prive', [DestinationController::class, 'destinationsPrivees']);
    Route::get('/departs/public', [DestinationController::class, 'departsPubliques']);
    Route::get('/departs/prive', [DestinationController::class, 'departsPrives']);
   Route::get('/compagnies-disponibles', [CompagnieController::class, 'compagniesDisponibles']);
Route::post('/paiement/callback', [PaiementCallbackController::class, 'confirm']);

 Route::post('/reservations', [ReservationsController::class, 'store']);
