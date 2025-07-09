<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Profils\ProfilsController;
use App\Http\Controllers\Permissions\PermissionsController;
use App\Http\Controllers\Voyages\VoyagesController;
use App\Http\Controllers\Bus\BusController;
use App\Http\Controllers\Compagnies\CompagniesController;
use App\Http\Controllers\Tickets\TicketController;
use App\Http\Controllers\Trajets\TrajetsController;
use App\Http\Controllers\Paiement\PaiementsController;
use App\Http\Controllers\Notes\NotesController;
use App\Http\Controllers\Notifications\NotificationsController;
use App\Http\Controllers\GarreTrajetsController;
use App\Http\Controllers\FrequenceTrajetsController;
use Illuminate\Support\Facades\Route;
use App\Models\Voyages;


//use Illuminate\Support\Facades\Route;
//use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
// use Intervention\Image\Facades\Image;
//use Image;
// use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManagerStatic as Image;




use App\Http\Controllers\Garres\GarresController;
use App\Http\Controllers\ReservationsController;
use Database\Seeders\GarresSeeder;

//ProfilsController   PermissionsController PaiementsController

use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;
//use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
//use App\Mail\TicketMail;
//use SimpleSoftwareIO\QrCode\Facades\QrCode;



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


Route::get('/user', [UserController::class, 'index'])->name('users.index');

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

Route::middleware(['auth'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    // Route::resource('users', UserController::class)
    //     ->names('users');
});

Route::middleware(['guest'])->group(function () {

    Route::get('login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate']);
//});





    Route::get('/', [AuthController::class, 'loginPage'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate']);
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.store');
});

    Route::resource('users', UserController::class);
    Route::resource('garres', GarresController::class);
    //  // Routes supplémentaires pour les utilisateurs
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
         ->name('users.toggleStatus');

    Route::get('users/search', [UserController::class, 'search'])
         ->name('users.search');
 // Routes pour la gestion des utilisateurs

