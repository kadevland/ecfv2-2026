<?php

declare(strict_types=1);

namespace App\Application\Salle\Queries\GetSallesList;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Salle\DTOs\SalleListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;

final class GetSallesListQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetSallesListQuery);

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'Paramètres de requête invalides'
                );
            }

            // Préparer les filtres
            $filters = [];

            if ($query->search) {
                $filters['search'] = $query->search;
            }

            if ($query->technologies) {
                $filters['technologies'] = $query->technologies;
            }

            if ($query->statut) {
                $filters['statut'] = $query->statut;
            }

            if ($query->accessibilitePmr !== null) {
                $filters['accessibilite_pmr'] = $query->accessibilitePmr;
            }

            if ($query->climatisation !== null) {
                $filters['climatisation'] = $query->climatisation;
            }

            if ($query->qualiteSon) {
                $filters['qualite_son'] = $query->qualiteSon;
            }

            if ($query->tailleEcran) {
                $filters['taille_ecran'] = $query->tailleEcran;
            }

            if ($query->typeEcran) {
                $filters['type_ecran'] = $query->typeEcran;
            }

            if ($query->filters) {
                $filters = array_merge($filters, $query->filters);
            }

            // Ajouter les critères de tri
            $filters['sort_by']        = $query->sortBy;
            $filters['sort_direction'] = $query->sortDirection;

            // Créer les critères de pagination avec filtres
            $criteria = new PaginationCriteria(
                page: $query->page,
                perPage: $query->perPage,
                filters: $filters
            );

            // Utiliser la méthode optimisée avec eager loading
            $result = $this->salleRepository->findWithPaginationAndCinemaNames($criteria);

            // Mapper les arrays vers DTOs
            $sallesDtos = array_map(
                fn (array $data) => new SalleListItemDto(
                    uuid: $data['uuid'],
                    nom: $data['nom'],
                    capaciteTotale: $data['capacite_totale'],
                    nombreRangees: $data['nombre_rangees'],
                    placesParRangee: $data['places_par_rangee'],
                    placesStandard: $data['places_standard'],
                    placesPmr: $data['places_pmr'],
                    qualiteProjection: $data['qualite_projection'],
                    qualiteSonore: $data['qualite_sonore'],
                    accessibilitePmr: $data['accessibilite_pmr'],
                    climatisation: $data['climatisation'],
                    planSalle: $data['plan_salle'],
                    statut: $data['statut'],
                    estDisponible: $data['est_disponible'],
                    cinemaNom: $data['cinema_nom'],
                ),
                $result['items']
            );

            // Créer la réponse paginée
            $response = new GetSallesListQueryResponse(
                salles: $sallesDtos,
                total: $result['total'],
                page: $result['page'],
                perPage: $result['per_page']
            );

            return Result::success($response);

        } catch (Exception $e) {
            Log::error('GetSallesListQueryHandler failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération de la liste: ' . $e->getMessage()
            );
        }
    }
}
