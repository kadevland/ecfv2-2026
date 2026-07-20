<?php

declare(strict_types=1);

namespace App\Application\Public\Seance\Queries\GetSeancesByFilm;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Application\Public\Seance\DTOs\SeancePublicDto;
use App\Domain\Public\Repositories\SeancePublicRepositoryInterface;

final class GetSeancesByFilmQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly SeancePublicRepositoryInterface $seanceRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetSeancesByFilmQuery) {
            return Result::error(
                'INVALID_QUERY_TYPE',
                'Type de requête invalide'
            );
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'Paramètres de requête invalides'
                );
            }

            // Récupérer les séances depuis MongoDB
            $seancesData = $this->seanceRepository->findByFilmId(
                $query->filmId,
                $query->futuresOnly ?? true,
                $query->limit
            );

            if (empty($seancesData['seances'])) {
                return Result::success(new GetSeancesByFilmQueryResponse(
                    filmId: $query->filmId,
                    filmTitre: $seancesData['film_titre'],
                    seances: [],
                    totalCount: 0
                ));
            }

            // Mapper vers DTOs
            $seancesDtos = array_map(
                fn (array $seanceData) => $this->mapToDto($seanceData),
                $seancesData['seances']
            );

            $response = new GetSeancesByFilmQueryResponse(
                filmId: $query->filmId,
                filmTitre: $seancesData['film_titre'],
                seances: $seancesDtos,
                totalCount: $seancesData['total_count']
            );

            return Result::success($response);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération des séances: ' . $e->getMessage()
            );
        }
    }

    /**
     * @param array<string, mixed> $seanceData
     */
    private function mapToDto(array $seanceData): SeancePublicDto
    {
        return new SeancePublicDto(
            seanceId: $seanceData['seance_id'],
            filmId: $seanceData['film_id'],
            salleId: $seanceData['salle_id'],
            cinemaId: $seanceData['cinema_id'],
            filmTitre: $seanceData['film_titre'],
            salleNom: $seanceData['salle_nom'],
            cinemaNom: $seanceData['cinema_nom'],
            dateHeureDebut: $seanceData['date_heure_debut'],
            dateHeureFin: $seanceData['date_heure_fin'],
            version: $seanceData['version'],
            technologies: $seanceData['technologies'] ?? [],
            tarification: $seanceData['tarification'] ?? [],
            statut: $seanceData['statut'],
            placesTotales: $seanceData['places_totales'] ?? 0,
            placesDisponibles: $seanceData['places_disponibles'] ?? 0,
            estComplete: $seanceData['est_complete'] ?? false,
            placementLibre: $seanceData['placement_libre'] ?? false,
        );
    }
}
