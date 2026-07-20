<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetSeances;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Entities\Seance;
use App\Application\Contracts\QueryInterface;
use App\Application\Cinema\DTOs\SeanceListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;

final class GetSeancesQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly SeanceRepositoryInterface $seanceRepository,
        private readonly FilmRepositoryInterface $filmRepository,
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly CinemaRepositoryInterface $cinemaRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetSeancesQuery);

        try {
            // Préparer les filtres
            $filters = [];

            if ($query->filmId) {
                $filters['film_id'] = $query->filmId;
            }

            if ($query->salleId) {
                $filters['salle_id'] = $query->salleId;
            }

            if ($query->dateFrom) {
                $filters['date_from'] = $query->dateFrom;
            }

            if ($query->dateTo) {
                $filters['date_to'] = $query->dateTo;
            }

            if ($query->statut) {
                $filters['statut'] = $query->statut;
            }

            // Créer les critères de pagination avec filtres
            $criteria = new PaginationCriteria(
                page: $query->page,
                perPage: $query->perPage,
                filters: $filters,
                sortBy: $query->sortBy,
                sortDirection: $query->sortDirection,
            );

            // Utiliser le repository
            $paginatedCollection = $this->seanceRepository->findWithPagination($criteria);

            // Mapper les entités vers DTOs
            $seancesDtos = array_map(
                fn (Seance $seance) => $this->mapToDto($seance),
                $paginatedCollection->items
            );

            // Créer la réponse paginée
            $response = new GetSeancesQueryResponse(
                seances: $seancesDtos,
                total: $paginatedCollection->total,
                page: $paginatedCollection->currentPage(),
                perPage: $paginatedCollection->perPage()
            );

            return Result::success($response);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération des séances: ' . $e->getMessage()
            );
        }
    }

    private function mapToDto(Seance $seance): SeanceListItemDto
    {
        // Récupérer le film
        $film      = $this->filmRepository->findById($seance->filmId);
        $filmTitre = $film ? $film->titre : 'Film inconnu';

        // Récupérer la salle
        $salle    = $this->salleRepository->findById($seance->salleId);
        $salleNom = $salle ? $salle->nom : 'Salle inconnue';

        // Récupérer le cinéma via la salle
        $cinema     = null;
        $cinemaNom  = 'Cinéma inconnu';
        $cinemaUuid = '';

        if ($salle) {
            $cinema = $this->cinemaRepository->findById($salle->cinemaId);
            if ($cinema) {
                $cinemaNom  = $cinema->nom;
                $cinemaUuid = $cinema->id->value;
            }
        }

        // Convertir la tarification en array
        $tarification = [
            'normal' => $seance->getPrixNormal()?->getAmount() / 100,
            'reduit' => $seance->getPrixReduit()?->getAmount() / 100,
            'senior' => $seance->getPrixSenior()?->getAmount() / 100,
            'enfant' => $seance->getPrixEnfant()?->getAmount() / 100,
            'pmr'    => $seance->getPrixPMR()?->getAmount() / 100,
        ];

        return new SeanceListItemDto(
            uuid: $seance->id->value,
            filmUuid: $seance->filmId->value,
            filmTitre: $filmTitre,
            salleUuid: $seance->salleId->value,
            salleNom: $salleNom,
            cinemaUuid: $cinemaUuid,
            cinemaNom: $cinemaNom,
            dateHeureDebut: $seance->dateHeureDebut->format('Y-m-d H:i:s'),
            dateHeureFin: $seance->dateHeureFin->format('Y-m-d H:i:s'),
            version: $seance->version,
            tarification: $tarification,
            placementLibre: $seance->placementLibre,
            statut: $seance->statut->value,
            optionsSupplementaires: null,
        );
    }
}
