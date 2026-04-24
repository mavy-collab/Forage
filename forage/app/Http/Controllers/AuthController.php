<?php

namespace App\Http\Controllers;

use App\Models\JournalActivite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS  = 10;
    private const LOCKOUT_MINS  = 15;

    private function lockoutKey(Request $request): string
    {
        // Clé basée sur l'email uniquement — résiste aux changements d'IP/VPN
        return 'login_attempts_' . md5(strtolower($request->input('email', '')));
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'email.email'       => 'L\'email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Vérifier si le compte est temporairement verrouillé
        $key      = $this->lockoutKey($request);
        $attempts = Cache::get($key, 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            return back()->withErrors([
                'email' => 'Trop de tentatives échouées. Compte bloqué pendant ' . self::LOCKOUT_MINS . ' minutes.',
            ])->onlyInput('email');
        }

        // Temps de réponse constant (résiste aux timing attacks)
        $start   = microtime(true);
        $success = Auth::attempt($credentials, $request->boolean('remember'));
        $elapsed = microtime(true) - $start;
        if ($elapsed < 0.3) {
            usleep((int) ((0.3 - $elapsed) * 1_000_000));
        }

        if ($success) {
            Cache::forget($key);
            $request->session()->regenerate();
            JournalActivite::enregistrer('connexion', 'Connexion de ' . Auth::user()->name);
            return redirect()->intended(route('dashboard'));
        }

        // Incrémenter le compteur d'échecs
        Cache::put($key, $attempts + 1, now()->addMinutes(self::LOCKOUT_MINS));

        $restants = self::MAX_ATTEMPTS - ($attempts + 1);
        $msg = $restants > 0
            ? "Email ou mot de passe incorrect. ({$restants} tentatives restantes)"
            : 'Trop de tentatives. Compte bloqué pendant ' . self::LOCKOUT_MINS . ' minutes.';

        return back()->withErrors(['email' => $msg])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        JournalActivite::enregistrer('connexion', 'Déconnexion de ' . Auth::user()->name);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
