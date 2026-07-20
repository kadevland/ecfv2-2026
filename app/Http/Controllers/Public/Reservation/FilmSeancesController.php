<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public\Reservation;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Domain\Public\Repositories\SeanceRepositoryInterface;

/**
 * Contrôleur pour récupérer les séances d'un film
 * RESPONSABILITÉ UNIQUE : Fournir les séances disponibles pour un film
 */
class FilmSeancesController extends Controller
{
    public function __construct(
        protected SeanceRepositoryInterface $seanceRepository
    ) {}

    /**
     * Retourne les séances d'un film pour la réservation
     * GET /reservation/film/{filmId}/seances
     */
    public function __invoke(string $filmId): JsonResponse
    {
        // Récupérer les séances via repository (sans filtre de date pour le moment)
        $seances = $this->seanceRepository->getFilmSeancesPublic($filmId);

        // Grouper par cinéma et par jour
        $seancesGroupees = [];
        foreach ($seances as $seance) {
            $date     = \Carbon\Carbon::parse($seance->date_heure_debut)->format('Y-m-d');
            $cinemaId = $seance->cinema_id;

            if (!isset($seancesGroupees[$cinemaId])) {
                $seancesGroupees[$cinemaId] = [
                    'cinema_nom' => $seance->cinema_nom,
                    'dates'      => [],
                ];
            }

            if (!isset($seancesGroupees[$cinemaId]['dates'][$date])) {
                $seancesGroupees[$cinemaId]['dates'][$date] = [];
            }

            $seancesGroupees[$cinemaId]['dates'][$date][] = [
                'seance_id'          => $seance->seance_id,
                'heure'              => \Carbon\Carbon::parse($seance->date_heure_debut)->format('H:i'),
                'salle'              => $seance->salle_nom,
                'version'            => $seance->version ?? 'VF',
                'qualite'            => $seance->qualite_projection ?? '2D',
                'places_disponibles' => $seance->places_disponibles,
            ];
        }

        return response()->json([
            'film_id' => $filmId,
            'seances' => $seancesGroupees,
            'total'   => $seances->count(),
        ]);
    }
}
