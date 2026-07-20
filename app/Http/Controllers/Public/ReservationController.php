<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Application\Bus\QueryBus;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CreateReservationRequest;
use App\Application\Reservations\Queries\GetReservationQuery;
use App\Application\Reservations\Commands\CreateReservationCommand;
use App\Domain\Public\Repositories\SeanceRepositoryInterface;
use App\Domain\Public\Repositories\FilmRepositoryInterface;

class ReservationController extends Controller
{
    public function __construct(
        protected SeanceRepositoryInterface $seanceRepository,
        protected FilmRepositoryInterface $filmRepository
    ) {}

    /**
     * Affiche la sélection des places pour une séance
     */
    public function showSeatSelection(Request $request, string $seanceId): View
    {
        // Récupérer séance via repository
        $seance = $this->seanceRepository->getSeancePublicData($seanceId);

        if (!$seance) {
            abort(404, 'Séance non trouvée');
        }

        // Récupérer film via repository
        $film = $this->filmRepository->find($seance->film_id);

        // Récupérer les tarifs depuis la séance


        $tarifs = [];
        if (isset($seance->tarifs) && isset($seance->tarifs['tarifs_base'])) {
            $tarifsBase = $seance->tarifs['tarifs_base'];
            // Récupérer uniquement les tarifs disponibles
            if (isset($tarifsBase['normal'])) {
                $tarifs['normal'] = $tarifsBase['normal'] / 100; // Convertir de centimes en euros
            }
            if (isset($tarifsBase['reduit'])) {
                $tarifs['reduit'] = $tarifsBase['reduit'] / 100;
            }
            if (isset($tarifsBase['enfant'])) {
                $tarifs['enfant'] = $tarifsBase['enfant'] / 100;
            }
        }
        // Si pas de tarifs définis, utiliser les tarifs par défaut
        if (empty($tarifs)) {
            $tarifs = [
                'normal' => 12.50,
                'reduit' => 9.50,
                'enfant' => 7.50,
            ];
        }

        $seanceData = [
            'seance_id'    => $seance->seance_id,
            'film_id'      => $seance->film_id,
            'titre'        => $film->titre ?? 'Film inconnu',
            'poster_url'   => $film->affiche_url ?? null,
            'backdrop_url' => $film->affiche_url ?? null,
            'genre'        => is_array($film->genres ?? []) && !empty($film->genres)
                                    ? $film->genres[0] : null,
            'duree'              => $film->duree_minutes ?? null,
            'classification'     => $film->classification ?? null,
            'date_heure'         => $seance->date_heure_debut,
            'salle'              => $seance->salle_nom,
            'qualite_projection' => $seance->qualite_projection ?? '2D',
            'version'            => $seance->version ?? 'VF',
            'tarif'              => max($tarifs), // Garde pour compatibilité
            'tarifs'             => $tarifs, // Nouveaux tarifs multiples
            'cinema_nom'         => $seance->cinema_nom,
            'places_disponibles' => $seance->places_disponibles,
            'places_occupees'    => $seance->places_occupees ?? [],
            'salle_type'         => $seance->salle_type ?? 'libre', // 'libre' ou 'numerotee'
        ];

        return view('public.seance.places', [
            'seance' => $seanceData,
        ]);
    }

    /**
     * Crée une réservation en utilisant CQRS
     */
    public function createReservation(CreateReservationRequest $request, CommandBus $commandBus, QueryBus $queryBus): RedirectResponse
    {
        try {
            // 1. Gestion utilisateur - vérifier l'authentification
            if (!auth()->check()) {
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté pour effectuer une réservation');
            }

            // Trouver le UserProfil correspondant à l'utilisateur connecté
            $user       = auth()->user();
            $userProfil = \App\Infrastructure\Database\Models\Profiles\UserProfil::where(
                \App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::USER_ID,
                $user->id
            )->first();

            if (!$userProfil) {
                // Si le profil n'existe pas encore, le créer automatiquement
                $userProfil = \App\Infrastructure\Database\Models\Profiles\UserProfil::create([
                    \App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::USER_ID => $user->id,
                    \App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::NOM     => $user->nom ?? 'Unknown',
                    \App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::PRENOM  => $user->prenom ?? 'Unknown',
                    \App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::EMAIL   => $user->email,
                ]);
            }

            $userId = $userProfil->{\App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema::ID};

            // 2. Créer et dispatcher la commande
            $command = CreateReservationCommand::fromRequest([
                'user_id'       => (string) $userId,
                'seance_id'     => $request->seance_id,
                'seats'         => $request->seats ?? null,
                'nombre_places' => $request->nombre_places ?? null,
                'places'        => $request->places ?? null, // Nouveau format multi-tarifs
            ]);

            // 3. Le handler gère toute la logique métier et validation
            $result = $commandBus->dispatch($command);

            // 4. Vérifier le résultat
            if ($result->isError()) {
                return back()
                    ->withErrors(['error' => $result->getErrorMessage()])
                    ->withInput();
            }

            // 5. Récupérer les infos pour la confirmation via Query CQRS
            $reservationId = $result->getValue();

            // Utiliser une Query pour récupérer la réservation (pattern CQRS correct)
            $getReservationQuery = new GetReservationQuery($reservationId);
            $reservationResult   = $queryBus->ask($getReservationQuery);

            if ($reservationResult === null || $reservationResult->isError()) {
                return back()->withErrors(['error' => 'Réservation non trouvée après création'])->withInput();
            }

            $reservation = $reservationResult->getValue();

            return redirect()->route('reservation.confirmation')
                ->with([
                    'reservation_id'     => $reservationId,
                    'numero_reservation' => $reservation->numeroReservation, // Propriété de l'entité
                    'success'            => 'Réservation confirmée avec succès !',
                ]);

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la réservation'])->withInput();
        }
    }

    /**
     * Page de confirmation
     */
    public function confirmation(Request $request): View|RedirectResponse
    {
        if (!session('success')) {
            return redirect()->route('home');
        }

        $reservationId     = session('reservation_id');
        $numeroReservation = session('numero_reservation');

        return view('public.reservation.confirmation', [
            'reservation_id'     => $reservationId,
            'numero_reservation' => $numeroReservation,
        ]);
    }
}
