<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Account;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Infrastructure\Database\Models\Auth\UserCredential;
use App\Infrastructure\Database\Models\Reservations\Reservation;

/**
 * Contrôleur pour afficher la page Mon Compte
 * RESPONSABILITÉ UNIQUE : Afficher le compte utilisateur avec ses réservations
 */
class ShowAccountController extends Controller
{
    /**
     * Affiche la page Mon Compte
     * GET /mon-compte
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Récupérer le profil complet de l'utilisateur
        $profil = \App\Infrastructure\Database\Models\Profiles\UserProfil::where('user_uuid', $user->id)->first();

        // Récupérer les credentials pour l'email
        $credential = UserCredential::where('user_uuid', $user->id)->first();

        // Fusionner les données du user avec son profil
        $userWithProfile = (object) [
            'id'             => $user->id,
            'email'          => $credential?->email ?? 'Email non disponible',
            'type'           => $user->type,
            'prenom'         => $profil?->prenom ?? null,
            'nom'            => $profil?->nom ?? null,
            'telephone'      => $profil?->telephone ?? null,
            'date_naissance' => $profil?->date_naissance ?? null,
            'adresse'        => $profil?->adresse ?? [],
        ];

        // Compter les réservations pour l'affichage (utiliser les vrais statuts)
        // $activeCount = Reservation::where('user_uuid', $user->id)
        //     ->whereIn('statut', ['EN_ATTENTE_PAIEMENT', 'CONFIRMEE', 'PAYEE'])
        //     ->count();

        return view('public.account.index', [
            'user' => $userWithProfile,
            // 'activeCount' => $activeCount,
        ]);
    }
}
