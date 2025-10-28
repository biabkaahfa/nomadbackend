<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\Api\CompagnieController;
use App\Http\Controllers\AnalyseFeedbackController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\Auth\JWTAuthController;
use App\Http\Controllers\PaiementCallbackController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ControllerApiController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\ValidationAbonnementController;
use App\Http\Controllers\Api\ImageControllerApiController;
use App\Http\Controllers\Api\AbonnementPublicControllerApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetToken']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPasswordWithToken']);

Route::middleware(['auth:jwt'])->group(function () {
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::post('refresh', [JWTAuthController::class, 'refresh']);
    Route::post('me', [JWTAuthController::class, 'me']);
});

Route::middleware(['guest'])->group(function () {
    Route::post('login', [JWTAuthController::class, 'login']);
    Route::post('loginController', [JWTAuthController::class, 'loginController']);
});

Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth:jwt')->group(function () {

    // === ROUTES DU CONTRÔLEUR ===

    // Profil contrôleur
    Route::get('/profile', [ControllerApiController::class, 'getProfile']);
    Route::put('/profileUpdate', [ControllerApiController::class, 'updateProfile']);

    // Statistiques - UNE SEULE DÉFINITION
    Route::get('/stats', [ControllerApiController::class, 'getScanStats']);

    // Scan de tickets
    Route::post('/scan-ticket', [ControllerApiController::class, 'scanTicket']);
    Route::get('/recent-scans', [ControllerApiController::class, 'getRecentScans']);
    Route::get('/ticket/{ticketId}/scan-history', [ControllerApiController::class, 'getTicketScanHistory']);

    // === AUTRES ROUTES ===
    Route::post('/user/upload-image', [UserController::class, 'uploadImage']);
    Route::post('/paiement/callback', [PaiementCallbackController::class, 'confirm']);
    Route::post('/paiement', [PaiementCallbackController::class, 'confirmerPaiement']);

    Route::get('/tickets', [TicketController::class, 'recuperationTickets']);
    Route::post('/tickets/{ticketId}/delete', [TicketController::class, 'markAsDeleted']);

    Route::get('/reservationsResumer', [ReservationsController::class, 'getUserReservations']);
    Route::get('/reservations/{id}/recapitulatif', [ReservationsController::class, 'recapitulatif']);
    Route::post('/reservations', [ReservationsController::class, 'store']);
    Route::post('/reservations/recapitulatif', [ReservationsController::class, 'recapitulatifTemporaire']);

    // Abonnements
    Route::get('/abonnement/actif', [AbonnementPublicControllerApiController::class, 'getAbonnementActif']);
    Route::get('/abonements/expires', [AbonnementPublicControllerApiController::class, 'getAbonnementsExpires']);
    Route::post('/subscriptions', [AbonnementPublicControllerApiController::class, 'confirmerPaiementAbonnement']);
    Route::get('/companies/public', [AbonnementPublicControllerApiController::class, 'getCompagniesPublics']);

    // Statistiques utilisateur
    Route::get('/stats/utilisateur', [StatsController::class, 'statsUtilisateur']);
});

// Routes publiques
Route::get('/images/{folder}/{filename}', [ImageControllerApiController::class, 'show'])
    ->where('folder', 'abonnements|assets|users')
    ->where('filename', '.*')
    ->name('api.images.show');

Route::get('/storage/photos/{filename}', function ($filename) {
    $path = storage_path('app/public/photos/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return response($file, 200)->header("Content-Type", $type);
});

// Destinations et compagnies publiques
Route::get('/destinations/public', [DestinationController::class, 'destinationsPubliques']);
Route::get('/destinations/prive', [DestinationController::class, 'destinationsPrivees']);
Route::get('/departs/public', [DestinationController::class, 'departsPubliques']);
Route::get('/departs/prive', [DestinationController::class, 'departsPrives']);
Route::get('/compagnies-disponibles', [CompagnieController::class, 'compagniesDisponibles']);


// routes/api.php
Route::middleware('auth:jwt')->prefix('analytics')->group(function () {
    Route::get('/feedback', [AnalyseFeedbackController::class, 'apiAnalyse']);
    Route::get('/feedback/rapport', [AnalyseFeedbackController::class, 'rapportComplet']);
    Route::get('/feedback/statistiques', [AnalyseFeedbackController::class, 'statistiques']);

    // FCM Token
    Route::post('/token', [FcmTokenController::class, 'store']);
    Route::post('/test', [FcmTokenController::class, 'test']);
}
);
