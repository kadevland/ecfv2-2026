<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\UserType;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Validation\ValidationException;

final class WebAuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm(Request $request): View
    {
        // Stocker l'URL de redirection dans la session si fournie
        if ($request->has('redirect')) {
            session(['url.intended' => $request->get('redirect')]);
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(LoginRequest $request): RedirectResponse
    {

        // Attempt authentication using our custom provider
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Check if user is active
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Votre compte est désactivé.'],
            ]);
        }

        $request->session()
            ->regenerate();

        // Redirection basée sur le type d'utilisateur
        $redirectUrl = $this->getRedirectUrlByUserType($user->type);

        return redirect()->intended($redirectUrl);
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm(Request $request): View
    {
        // Stocker l'URL de redirection dans la session si fournie
        if ($request->has('redirect')) {
            session(['url.intended' => $request->get('redirect')]);
        }

        return view('auth.register');
    }

    /**
     * Handle registration request (Demo site - blocked)
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        // DEMO SITE: Bloquer toutes les inscriptions avec message explicatif
        return redirect()
            ->route('register')
            ->with('error', 'Site de démonstration - Aucune inscription réelle ne peut être effectuée. Cette fonctionnalité sera disponible en production.');

        // Code pour la production (commenté pour la demo)
        /*
        // Les données sont automatiquement validées grâce au RegisterRequest
        $validated = $request->validated();

        // Créer l'utilisateur
        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'telephone' => $validated['telephone'],
            'date_naissance' => $validated['date_naissance'],
            'newsletter' => $validated['newsletter'] ?? false,
            'is_active' => true,
            'email_verified_at' => now(), // Auto-vérification pour la demo
        ]);

        // Log de l'inscription
        logger()->info('Nouvel utilisateur inscrit', [
            'user_id' => $user->id,
            'email' => $user->email,
            'newsletter' => $validated['newsletter'] ?? false
        ]);

        // Connexion automatique après inscription
        Auth::login($user);

        return redirect()->intended('/dashboard')->with('success', 'Votre compte a été créé avec succès ! Bienvenue sur Cinéphoria.');
        */
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): RedirectResponse
    {
        if(Auth::guest()){
            return redirect('/');
        }


        Auth::logout();

        $request->session()
            ->invalidate();
        $request->session()
            ->regenerateToken();

        return redirect('/');
    }

    /**
     * Détermine l'URL de redirection selon le type d'utilisateur
     */
    private function getRedirectUrlByUserType(UserType $userType): string
    {
        return match ($userType) {
            UserType::EMPLOYEE => route('employee.dashboard'), // /employee/dashboard
            UserType::ADMIN    => route('admin.dashboard'), // Administration dashboard
            UserType::CLIENT   => route('account'), // Page compte client (pas de dashboard)
            default            => '/',
        };
    }
}
