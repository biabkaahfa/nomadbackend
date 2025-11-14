<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Tymon\JWTAuth\JWT;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class JWTAuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = Auth::guard('jwt')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // return $this->respondWithToken($token);
        return response()->json([
    'token' => $token,
    'user' => Auth::guard('jwt')->user()
]);

    }
    public function loginController(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier si l'utilisateur existe
        $user = User::with('profil')
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        // Vérifier le profil (doit être contrôleur)
        if (!$user->profil || $user->profil->name !== 'Contrôleur') {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès réservé aux contrôleurs'
            ], 403);
        }

        // Vérifier le statut (doit être actif)
        if ($user->statut !== 'actif') {
            return response()->json([
                'status' => 'error',
                'message' => 'Votre compte est inactif. Contactez l\'administrateur.'
            ], 403);
        }

        // Vérifier qu'il a une compagnie et une gare assignée
        if (!$user->idCompagnie || !$user->idGarre) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contrôleur non assigné à une compagnie/gare'
            ], 403);
        }

        try {
            // Générer le token avec expiration de 1 jour pour les contrôleurs
            $customClaims = [
                'user_type' => 'controller',
                'exp' => now()->addDay()->timestamp // 1 jour
            ];

            $token = JWTAuth::claims($customClaims)->fromUser($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'telephone' => $user->telephone,
                        'profil' => $user->profil->name,
                        'statut' => $user->statut,
                        'compagnie_id' => $user->idCompagnie,
                        'garre_id' => $user->idGarre,
                        'image' => $user->image_url,
                    ],
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 86400, // 1 jour en secondes
                    'expires_at' => now()->addDay()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la génération du token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(Auth::guard('jwt')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard('jwt')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = app('tymon.jwt.auth')->refresh();
        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }
}
