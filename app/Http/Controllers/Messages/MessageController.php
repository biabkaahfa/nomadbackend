<?php

namespace App\Http\Controllers\Messages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messages\StoreMessageRequest;
use App\Http\Requests\Messages\UpdateMessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
  use Carbon\Carbon;

class MessageController extends Controller
{
    /**
     * Affiche la liste des messages
     */


// ...

public function index(): View
{
    $user = Auth::user();

    // Si l'utilisateur n'est ni Admin général ni Admin compagnie, on interdit l'accès
    if (!in_array($user->profil->name, ['Admin général', 'Admin compagnie'])) {
        abort(403, 'Accès non autorisé.');
    }

    $messages = Message::query()
        ->when($user->profil->name === 'Admin compagnie', function($query) use ($user) {
            $query->whereHas('abonement', function($q) use ($user) {
                $q->where('idCompagnie', $user->idCompagnie);
            });
        })
        ->with([
            'abonement.typeAbonement',
            'abonement.compagnie' => function($query) {
                $query->select('id', 'name');
            }
        ])
        ->latest('dateEnvoi')
        ->paginate(10);

    // Format de date pour l'affichage
    $messages->getCollection()->transform(function ($message) {
        $message->dateEnvoi = Carbon::parse($message->dateEnvoi);
        return $message;
    });

    return view('back.messages.index', compact('messages'));
}



    /**
     * Marque un message comme lu
     */
    public function marquerCommeLu(Message $message): JsonResponse
    {
        // Vérification plus robuste des permissions
        if (Auth::user()->profil->name === 'Admin compagnie' &&
            $message->abonement->idCompagnie !== Auth::user()->idCompagnie) {
            abort(403, 'Unauthorized action.');
        }

        $message->marquerCommeLu();

        return response()->json([
            'success' => true,
            'message' => 'Message marqué comme lu'
        ]);
    }
}
