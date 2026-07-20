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
 * Contrôleur pour afficher la page Mes Réservations
 * FAT CONTROLLER : Récupère toutes les infos nécessaires pour affichage
 */
class ShowReservationsController extends Controller
{
    /**
     * Affiche la page Mes Réservations avec toutes les infos
     * GET /mon-compte/reservations
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

        $userProfil = \App\Infrastructure\Database\Models\Profiles\UserProfil::where(
                \App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::USER_ID,
                $user->id
            )->first();
        $userId = $userProfil->{\App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::ID};

        // Fusionner les données du user avec son profil
        $userWithProfile = (object) [
            'id'     => $userId,
            'email'  => $credential?->email ?? 'Email non disponible',
            'type'   => $user->type,
            'prenom' => $profil?->prenom ?? null,
            'nom'    => $profil?->nom ?? null,
        ];

        // Récupérer TOUTES les réservations de l'utilisateur par défaut
        // OPTIMISATION : Eager loading de toutes les relations en une seule query
        $query = Reservation::where('user_uuid', $userId)
            ->with(['seance.film', 'seance.salle.cinema']) // Eager loading des relations
            ->orderBy('created_at', 'desc');

        // Filtrer par statut SEULEMENT si explicitement demandé
        if ($request->has('statut') && !empty($request->query('statut'))) {
            $query->where('statut', $request->query('statut'));
        }

        $reservations = $query->paginate(15);

        //dd($reservations,$user->id);

        // FAT CONTROLLER : Enrichir chaque réservation avec toutes les infos nécessaires
        foreach ($reservations as $reservation) {
            // Les données sont déjà chargées grâce au eager loading - pas de query supplémentaire !
            $seance = $reservation->seance;

            if ($seance) {
                // Attacher les objets directement à la réservation pour la vue
                $reservation->seance_data = $seance;
                $reservation->film_data   = $seance->film;
                $reservation->salle_data  = $seance->salle;
                $reservation->cinema_data = $seance->salle?->cinema;

                // Infos formatées pour affichage direct
                $reservation->film_titre          = $seance->film?->titre ?? 'Film inconnu';
                $reservation->film_duree          = $seance->film?->getFormattedDurationAttribute() ?? 'N/A';
                $reservation->film_classification = $seance->film?->classification ?? 'N/A';
                $reservation->film_affiche        = $seance->film?->affiche_url;

                $reservation->cinema_nom   = $seance->salle?->cinema?->nom ?? 'Cinéma inconnu';
                $reservation->cinema_ville = $seance->salle?->cinema?->ville ?? 'Ville inconnue';

                $reservation->salle_nom = $seance->salle?->nom ?? 'Salle inconnue';

                $reservation->seance_date    = $seance->date_heure_debut?->format('d/m/Y') ?? 'Date inconnue';
                $reservation->seance_heure   = $seance->date_heure_debut?->format('H:i') ?? 'Heure inconnue';
                $reservation->seance_version = $seance->version ?? 'VF';

                // Prix formaté depuis la réservation elle-même
                $reservation->prix_total_formate = number_format($reservation->prix_total_ttc_centimes / 100, 2, ',', ' ') . ' €';

                // Places
                $reservation->places_formate = $reservation->nombre_places . ' ' . ($reservation->nombre_places > 1 ? 'places' : 'place');

                // Détails des sièges si disponibles
                if (is_array($reservation->details_places) && isset($reservation->details_places['places'])) {
                    $seats = [];
                    foreach ($reservation->details_places['places'] as $place) {
                        if (isset($place['rangee']) && isset($place['numero'])) {
                            $seats[] = $place['rangee'] . $place['numero'];
                        }
                    }
                    $reservation->sieges_formate = !empty($seats) ? 'Sièges : ' . implode(', ', $seats) : null;
                } else {
                    $reservation->sieges_formate = null;
                }
            } else {
                // Valeurs par défaut si séance non trouvée
                $reservation->film_titre          = 'Film supprimé';
                $reservation->film_duree          = 'N/A';
                $reservation->film_classification = 'N/A';
                $reservation->film_affiche        = null;
                $reservation->cinema_nom          = 'Cinéma supprimé';
                $reservation->cinema_ville        = 'N/A';
                $reservation->salle_nom           = 'Salle supprimée';
                $reservation->seance_date         = 'Date inconnue';
                $reservation->seance_heure        = 'Heure inconnue';
                $reservation->seance_version      = 'N/A';
                $reservation->prix_total_formate  = number_format($reservation->prix_total_ttc_centimes / 100, 2, ',', ' ') . ' €';
                $reservation->places_formate      = $reservation->nombre_places . ' ' . ($reservation->nombre_places > 1 ? 'places' : 'place');
                $reservation->sieges_formate      = null;
            }
        }

        // Compter les réservations par statut (utiliser les vrais statuts de la DB)
        $activeCount = Reservation::where('user_uuid', $userId)
            ->whereIn('statut', ['EN_ATTENTE_PAIEMENT', 'CONFIRMEE', 'PAYEE'])
            ->count();

        $pastCount = Reservation::where('user_uuid', $userId)
            ->whereIn('statut', ['UTILISEE', 'EXPIREE', 'ANNULEE'])
            ->count();

        $allCount = Reservation::where('user_uuid', $userId)->count();

        return view('public.account.reservations', [
            'user'           => $userWithProfile,
            'reservations'   => $reservations,
            'activeCount'    => $activeCount,
            'pastCount'      => $pastCount,
            'allCount'       => $allCount,
            'selectedStatus' => $request->query('statut'),
        ]);
    }
}
