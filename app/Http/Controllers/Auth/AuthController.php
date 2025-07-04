<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        // Envoyer le lien de réinitialisation
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => 'Un lien de réinitialisation a été envoyé à votre adresse e-mail.'])
                    : back()->withErrors(['email' => 'Impossible d\'envoyer le lien de réinitialisation.']);
    }

    /**
     * Display the password reset view.
     */
    public function resetPassword(string $token): View
    {
        return view('pages.auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Réinitialiser le mot de passe
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès.')
                    : back()->withErrors(['email' => 'Une erreur s\'est produite lors de la réinitialisation.']);
    }
}
