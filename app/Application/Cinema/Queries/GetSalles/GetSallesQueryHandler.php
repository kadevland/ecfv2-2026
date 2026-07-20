<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetSalles;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Entities\Salle;
use App\Application\Contracts\QueryInterface;
use App\Application\Cinema\DTOs\SalleListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

final class GetSallesQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly CinemaRepositoryInterface $cinemaRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetSallesQuery);

        try {
            // Préparer les filtres
            $filters = [];

            if ($query->cinemaId) {
                $filters['cinema_id'] = $query->cinemaId;
            }

            if ($query->statut) {
                $filters['statut'] = $query->statut;
            }

            if ($query->technologies) {
                $filters['technologies'] = $query->technologies;
            }

            if ($query->accessibilitePmr !== null) {
                $filters['accessibilite_pmr'] = $query->accessibilitePmr;
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
            $paginatedCollection = $this->salleRepository->findWithPagination($criteria);

            // Mapper les entités vers DTOs
            $sallesDtos = array_map(
                fn (Salle $salle) => $this->mapToDto($salle),
                $paginatedCollection->items
            );

            // Créer la réponse paginée
            $response = new GetSallesQueryResponse(
                salles: $sallesDtos,
                total: $paginatedCollection->total,
                page: $paginatedCollection->currentPage(),
                perPage: $paginatedCollection->perPage()
            );

            return Result::success($response);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération des salles: ' . $e->getMessage()
            );
        }
    }

    private function mapToDto(Salle $salle): SalleListItemDto
    {
        // Récupérer le cinéma pour obtenir son nom
        $cinema    = $this->cinemaRepository->findById($salle->cinemaId);
        $cinemaNom = $cinema ? $cinema->nom : 'Cinéma inconnu';

        return new SalleListItemDto(
            uuid: $salle->id->value,
            nom: $salle->nom,
            numero: 1,
            capaciteTotale: $salle->capaciteTotale,
            technologies: array_merge(
                array_map(fn ($q) => $q->value, $salle->qualiteProjection),
                array_map(fn ($q) => $q->value, $salle->qualiteSonore)
            ),
            accessibilitePmr: $salle->accessibilitePmr,
            climatisation: $salle->climatisation,
            qualiteSon: !empty($salle->qualiteSonore) ? $salle->qualiteSonore[0]->value : 'standard',
            tailleEcran: 'standard',
            typeEcran: 'standard',
            configurationSieges: $salle->planSalle,
            tarifSupplement: null,
            statut: $salle->statut->value,
            cinemaUuid: $salle->cinemaId->value,
            cinemaNom: $cinemaNom,
        );
    }
}
