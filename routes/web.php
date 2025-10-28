<?php

use App\Models\Voyages;
use App\Mail\TicketMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Database\Seeders\GarresSeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Bus\BusController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
//use App\Http\Controllers\AbonementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\Notes\NotesController;
use App\Http\Controllers\profileUserController;
use App\Http\Controllers\GarreTrajetsController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\Garres\GarresController;
use App\Http\Controllers\Tickets\TicketController;
//AbonementController

//use Illuminate\Support\Facades\Route;
//use Intervention\Image\Facades\Image;
use App\Http\Controllers\AbonementPublicController;
// use Intervention\Image\Facades\Image;
//use Image;
// use Intervention\Image\Facades\Image;
use App\Http\Controllers\AnalyseFeedbackController;




use App\Http\Controllers\Profils\ProfilsController;
use App\Http\Controllers\Trajets\TrajetsController;
use App\Http\Controllers\Voyages\VoyagesController;

//ProfilsController   PermissionsController PaiementsController

//use App\Http\Controllers\Messages\MessageController;
use Intervention\Image\ImageManagerStatic as Image;
//use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\FrequenceTrajetsController;
use App\Http\Controllers\Messages\MessageController;
use App\Http\Controllers\Paiement\PaiementsController;
use App\Http\Controllers\Abonements\AbonementController;
use App\Http\Controllers\ValidationAbonnementController;
use App\Http\Controllers\Compagnies\CompagniesController;
use App\Http\Controllers\Permissions\PermissionsController;
use App\Http\Controllers\Notifications\NotificationsController;
use App\Http\Controllers\TypeAbonements\TypeAbonementController;
//use App\Mail\TicketMail;
//use SimpleSoftwareIO\QrCode\Facades\QrCode;








Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Fichier: routes/web.php
Route::group(['middleware' => 'auth'], function() {

    Route::resource('/messages',MessageController::class);
    Route::post('/messages/{message}/lu', [MessageController::class, 'marquerCommeLu'])->name('messages.lu');
});







// Affiche la liste des personnalisations de cartes
Route::get('/personalisationcards', [AbonementPublicController::class, 'index'])->name('personalisationCard.index');

// Routes pour les personnalisations de carte
Route::get('/personalisationcards', [AbonementPublicController::class, 'indexe'])
    ->name('personalisationCard.indexe');

Route::get('/personalisationcards/{perso}/modifier', [AbonementPublicController::class, 'modifier'])
    ->name('personalisationCard.modifier');

Route::put('/personalisationcards/{perso}', [AbonementPublicController::class, 'updateCarde'])
    ->name('personalisationCard.update');


Route::delete('/personalisationcards/{personalisationCard}', [AbonementPublicController::class, 'destroyPersonalisationCard'])
    ->name('personalisationCard.destroy');

// Affiche le formulaire de création d'une nouvelle personnalisation
//Route::get('/personalisationcards/create', [AbonementPublicController::class, 'c/reate'])->name('personalisationCard.create');

// Enregistre une nouvelle personnalisation de carte
Route::post('/personalisationcards', [AbonementPublicController::class, 'storePersonalisationCard'])->name('personalisationCard.store');

Route::get('/personalisationca', [AbonementPublicController::class, 'createPerso'])->name('abonementPublic.createPerso');

// Affiche le formulaire de modification d'une personnalisation de carte existante
// Le paramètre {personalisationCard} est l'ID de la carte à modifier
Route::get('/personalisationcards/{personalisationCard}/edit', [AbonementPublicController::class, 'editPersonalisationCard'])->name('personalisationCard.edit');

// Met à jour la personnalisation de carte existante
// Utilise la méthode PUT pour la mise à jour des ressources
Route::put('/personalisationcards/{personalisationCard}', [AbonementPublicController::class, 'updatePersonalisationCard'])->name('personalisationCard.update');

// Vous pouvez aussi utiliser une route de ressource pour simplifier
// Route::resource('personalisationCard', AbonementPublicController::class)->except(['show', 'destroy']);
// Assurez-vous simplement que les noms de méthodes dans le contrôleur correspondent.





    // web.php
Route::get('/profile/edit', [profileUserController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [profileUserController::class, 'update'])->name('profile.update');
Route::put('/profile/passwordUpdate', [profileUserController::class, 'passwordUpdate'])->name('profile.passwordUpdate');

    // Route::resource('profile', profileUserController::class);
    Route::resource('garres', GarresController::class);
    //routes abonnements public
    Route::resource('abonementPublic', AbonementPublicController::class);
    //  // Routes supplémentaires pour les utilisateurs
    Route::patch('user/{user}/toggle-status', [UserController::class, 'toggleStatus'])
         ->name('users.toggleStatus');

    Route::get('user/search', [UserController::class, 'search'])
         ->name('users.search');
       //   Route::get('/user', [UserController::class, 'index'])->name('usi.index');

Route::resource('user', UserController::class);

Route::get('/test-image', function () {
    $img = Image::canvas(100, 100, '#ff0000');
    return $img->response('png');
});
Route::get('/ticket-test1', function () {
    return response(
        QrCode::format('png')->size(300)->generate('TICKET-NOMADE')
    )->header('Content-Type', 'image/png');
});

Route::get('/ticket-test', function () {
    $qrCode = QrCode::format('png')->size(150)->generate('TICKET-NOMADE');

    $image = Image::canvas(600, 400, '#ffffff');
    $image->insert(Image::make($qrCode), 'bottom-left', 20, 20);

    $image->text('NOMADE TRANSPORT', 300, 50, function ($font) {
        // Si la police n’existe pas encore, commente la ligne ci-dessous
        //$font->file(public_path('fonts/arial.ttf'));
        $font->size(24);
        $font->color('#000000');
        $font->align('center');
    });

    $image->text('Client : Wenceslas', 300, 90, function ($font) {
        $font->size(18);
        $font->color('#555555');
        $font->align('center');
    });

    return $image->response('png');
});
Route::resource('parametres', ParametresController::class);
Route::resource('type-abonements',TypeAbonementController::class);
    // ->except(['show'])
    // ->names('type-abonements');

Route::resource('type-abonements',TypeAbonementController::class);

Route::resource('abonnements',AbonementController::class);
Route::post('/abonnements/{abonement}/renew', [AbonementController::class, 'renew'])->name('abonnements.renew');

Route::resource('profils',ProfilsController::class);
Route::resource('profils',ProfilsController::class);
Route::resource('permissions', PermissionsController::class);
Route::resource('compagnies', CompagniesController::class);
Route::resource('voyages', VoyagesController::class);
Route::resource('affectation', GarreTrajetsController::class);

//affectations des Bus

Route::get('/voyages/{voyage}/affectation',[VoyagesController::class,'affectation'])->name('voyages.affectation');
Route::put('/voyages/{voyage}/affecter-bus', [VoyagesController::class, 'affecterBus'])->name('voyages.affecterBus');


Route::get('/voyages/{id}/prix', function ($id) {
    $voyage = Voyages::with('trajet')->findOrFail($id); // relation "trajet"

    if (!$voyage->trajet) {
        return response()->json(['prix' => 0]);
    }

    return response()->json([
        'prix' => $voyage->trajet->prix,
        'dateDepart' => $voyage->dateDepart,          // <-- ajoute ceci
        'heureDepart' => $voyage->heuresDepart        // <-- et ceci
    ]);
});

Route::resource('frequences', FrequenceTrajetsController::class);

Route::resource('buses', BusController::class);

Route::resource('paiements', PaiementsController::class);


Route::resource('trajets', TrajetsController::class);

Route::resource('tickets', TicketController::class);
Route::resource('notes',NotesController::class);

Route::resource('notifications',NotificationsController::class);

Route::get('/test-mail', function () {
    // Variables simulées pour le test
    $client = 'Client Test';
    $compagnie = (object) ['name' => 'Compagnie Test'];
    $date = now()->format('Y-m-d');
    $depart = 'Ouagadougou';
    $arrivee = 'Bobo-Dioulasso';
    $ticket_id = 123456;

    // Génération du QR Code encodé en base6403
    $qrData = [
        'ticket_id' => $ticket_id,
        'client' => $client,
        'compagnie' => $compagnie->name,
        'depart' => $depart,
        'arrivee' => $arrivee,
        'date' => $date,
    ];

    $qrCode = QrCode::format('png')->size(200)->generate(json_encode($qrData));

    // Générer le PDF depuis la vue
    $pdf = Pdf::loadView('back.pdf.ticket', [
        'client' => $client,
        'compagnie' => $compagnie,
        'date' => $date,
        'depart' => $depart,
        'arrivee' => $arrivee,
        'ticket_id' => $ticket_id,
        'qrCode' => $qrCode
    ]);

    // Envoyer l'e-mail
    Mail::to('nomadplateforme@gmail.com')->send(new TicketMail(
        $client,
        $compagnie,
        $date,
        $pdf->output()
    ));

    return "Email envoyé avec succès.";
});


 Route::get('validations-abonnement/statistiques', [ValidationAbonnementController::class, 'statistiques']);

});

// Route::middleware(['guest'])->group(function () {

//     Route::post('/', [AuthController::class, 'loginPage'])->name('login');
//   Route::post('login', [AuthController::class, 'authenticate']);//->name('l');
// //});





//     Route::post('/', [AuthController::class, 'loginPage'])->name('login');
//      Route::post('login', [AuthController::class, 'authenticate']);
//     Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
//     Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

//     Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
//     Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.store');
// });

// routes/web.php (pour l'administration)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/feedback-analytics', [AnalyseFeedbackController::class, 'dashboard'])
         ->name('admin.feedback.analytics');
});


Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'loginPage'])->name('login'); // <--- AJOUTE CETTE LIGNE
    Route::post('login', [AuthController::class, 'authenticate'])->name('loga');

    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::put('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');
});



