<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Profils; // 👈 importer le modèle Profils

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:12|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 🔍 Récupération dynamique du profil "client"
        $profilClient = Profils::where('name', 'Client')->first();

        if (!$profilClient) {
            return response()->json(['error' => 'Profil "client" non trouvé.'], 500);
        }

        // Création de l'utilisateur
       $validated = $validator->validated();

        // DB::beginTransaction();
        // DB::commit();
        // DB::rollBack();

    //   $t =  DB::transaction(function(){
    //     });

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'telephone' => $validated['telephone'],
        'password' => Hash::make($validated['password']),
        'idProfil' => $profilClient->id,
        'statut' => 'actif',
        'image' => null,
    ]);


        // Connexion automatique (token JWT)
        if (! $token = Auth::guard('jwt')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'Inscription réussie',
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
        ], 201);
    }
}
