<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Reservation;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;
use App\Infrastructure\Database\Models\MongoDB\SeancePublic;

/**
 * Contrôleur pour la page de réservation
 * RESPONSABILITÉ UNIQUE : Afficher la page de sélection des films pour réservation
 */
class ReservationIndexController extends Controller
{
    /**
     * Affiche la page de réservation
     * GET /reservation
     */
    public function __invoke(): View
    {
        $data = Cache::remember('reservation_data', 10 * 60, function () {
            // Récupérer les séances futures disponibles avec leurs films
            $seancesAvecFilms = SeancePublic::where('date_heure_debut', '>=', now())
                ->where('statut', 'CONFIRMEE')
                ->orderBy('cinema_nom')
                ->orderBy('film_titre')
                ->get();

            // Récupérer les films correspondants
            $filmIds = $seancesAvecFilms->pluck('film_id')->unique();
            $films   = FilmCatalogue::whereIn('film_id', $filmIds)
                ->where('est_actif', true)
                ->get()
                ->keyBy('film_id');

            // Grouper par cinéma
            $cinemaFilms = [];
            foreach ($seancesAvecFilms as $seance) {
                $cinemaId = $seance->cinema_id;
                $filmId   = $seance->film_id;

                // Récupérer le film complet depuis FilmCatalogue
                $film = $films->get($filmId);
                if (!$film) {
                    continue;
                }

                if (!isset($cinemaFilms[$cinemaId])) {
                    $cinemaFilms[$cinemaId] = [
                        'cinema_id' => $cinemaId,
                        'nom'       => $seance->cinema_nom,
                        'ville'     => '', // À récupérer depuis une autre source si nécessaire
                        'films'     => collect(),
                    ];
                }

                // Ajouter le film s'il n'y est pas déjà
                if (!$cinemaFilms[$cinemaId]['films']->contains('film_id', $filmId)) {
                    $cinemaFilms[$cinemaId]['films']->push($film);
                }
            }

            return [
                'cinemaFilms'  => collect($cinemaFilms)->sortBy('nom')->values(),
                'totalCinemas' => count($cinemaFilms),
                'totalFilms'   => $films->count(),
            ];
        });

        return view('public.reservation.index', $data);
    }
}
