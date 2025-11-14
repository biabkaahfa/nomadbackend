<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password as FacadesPassword;
use App\Models\User;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('pages.auth.login');
    }



    /**
     * Handle an authentication attempt.
     */
   public function authenticate(Request $request): RedirectResponse
{
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        // L'adresse email n'existe pas
        return back()->withErrors([
            'email' => 'Cet email n\'est pas enregistré.',
        ])->onlyInput('email');
    }

    if (!$user->statut=='actif') {
        // Le compte est désactivé
        return back()->withErrors([
            'email' => 'Votre compte n\'est pas actif. Veuillez contacter l\'administrateur.',
        ])->onlyInput('email');
    }

    if (!Hash::check($request->password, $user->password)) {
        // Mauvais mot de passe
        return back()->withErrors([
            'password' => 'Mot de passe incorrect.',
        ])->onlyInput('email');
    }

    // Tout est bon, on connecte l'utilisateur
    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->intended('/dashboard');
}

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function forgotPassword(): View
    {
        return view('pages.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    // Envoyer le lien de reset par email
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Vérifier si l'utilisateur existe et est actif
        $user = User::where('email', $request->email)->first();
        if (!$user || !$user->isActive()) {
            return back()->with('status', 'Si cette adresse existe, vous recevrez un lien de réinitialisation.');
        }

        // Envoyer l'email
        $status = FacadesPassword::sendResetLink($request->only('email'));

        return $status === FacadesPassword::RESET_LINK_SENT
            ? back()->with('status', 'Lien de réinitialisation envoyé !')
            : back()->withErrors(['email' => 'Erreur lors de l\'envoi.']);
    }

    // Afficher le formulaire de reset
    public function resetPassword(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email')
        ]);
    }

    // Traiter le nouveau mot de passe
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $status = FacadesPassword::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === FacadesPassword::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Mot de passe réinitialisé avec succès !')
            : back()->withErrors(['email' => 'Le lien est invalide ou expiré.']);
    }
}
